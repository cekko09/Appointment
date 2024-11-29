<template>
  <div class="appointment-form">
    <h2>Yeni Randevu Oluştur</h2>
    <form @submit.prevent="submitForm">
     
      <div>
        <label for="postcode">Randevu Adresi Posta Kodu:</label>
        <input v-model="appointment.postcode" id="postcode" @input="fetchPostcodeDetails" />
      </div>

     
      <div v-if="addresses.length === 0">
        <label for="address">Randevu Adresi:</label>
        <input v-model="appointment.address" id="address" readonly />
      </div>
      <div v-else-if="addresses.length > 0 && appointment.postcode && !addressSelectedFromMap">
  <label for="address-select">Adres Seç:</label>
  <select v-model="appointment.address" id="address-select" @change="handleAddressChange">
    <option value="" diasbled selected>Lütfen Adres Seçiniz</option>
    <option v-for="(address, index) in addresses" :key="index" :value="address">{{ address }}</option>
  </select>
</div>
<div v-if="loadingAddresses" class="loader">Adresler Yükleniyor...</div>
      
      <div>
        <label for="date">Randevu Tarihi:</label>
        <input type="datetime-local" v-model="appointment.date" @change="updateTimes" id="date" required />
      </div>

     
      <div>
        <label for="client-name">Müşteri Adı:</label>
        <input v-model="appointment.client.name" id="client-name" required />
      </div>
      <div>
        <label for="client-email">Müşteri Email:</label>
        <input v-model="appointment.client.email" id="client-email" required />
      </div>
      <div>
        <label for="client-phone">Müşteri Telefon:</label>
        <input v-model="appointment.client.phone" id="client-phone" required />
      </div>

     
      <div>
        <label for="employee">Emlakçı Çalışanı:</label>
        <select v-model="appointment.employee" id="employee" required>
          <option v-for="employee in employees" :key="employee.id" :value="employee">{{ employee.first_name }} {{ employee.last_name }}</option>
        </select>
      </div>

      
      <Map ref="mapComponent" @addressSelected="onAddressSelected" />

      
      <p>Ofis ile randevu adresi arası mesafe: {{ distance }} km</p>
      <p>Tahmini Seyahat Süresi: {{ travelDuration }} </p>
      <p>Ofisten Çıkış Zamanı: {{ estimatedDepartureTime }}</p>
      <p>Randevudan Sonra Müsait Olacağı Zaman: {{ estimatedAvailableTime }}</p>
      
      
      <button :disabled="!isFormValid" type="submit">Randevu Oluştur</button>
    </form>
  </div>
</template>

<script>
import Map from './Map.vue';
import axios from 'axios';

export default {
  name: 'AppointmentForm',
  components: {
    Map,
  },
  data() {
    return {
      appointment: {
        postcode: '',
        date: '',
        client: {
          name: '',
          email: '',
          phone: ''
        },
        employee: null,
        address: '',
      },
      employees: [],
      addresses: [], // Posta koduna göre adresleri tutacak dizi
      selectedAddress: null,
      distance: '0',
      travelDuration: '0',
      estimatedDepartureTime: '',
      estimatedAvailableTime: '',
      officeLocation: {
        lat: 51.5074, // Londra örnek konum
        lng: -0.1278,
      },
      addressSelectedFromMap: false,
      loadingAddresses: false,
    };
  },
  watch: {
    'appointment.postcode': function(newVal) {
      if (!newVal) {
        this.appointment.address = ''; 
        this.addressSelectedFromMap = false;
      }
    }
  },
  computed: {
    isFormValid() {
      return (
        this.appointment.date &&
        this.appointment.client.name &&
        this.appointment.client.email &&
        this.appointment.client.phone &&
        this.appointment.employee &&
        this.appointment.address
      );
    }
  },
  methods: {
    handleAddressChange(event) {
    this.selectAddress(event);  
    this.updateTimes();  
  },
  selectAddress() {
  const selected = this.appointment.address;
  if (selected) {
    
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: selected }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const location = results[0].geometry.location;
        const lat = location.lat();
        const lng = location.lng();

        // Haritaya marker ekleyin ve haritayı merkeze alın
        if (this.$refs.mapComponent && this.$refs.mapComponent.setMarker) {
          this.$refs.mapComponent.setMarker(lat, lng);
        }

        // Koordinatları güncelle
        this.selectedAddress = { lat, lng };

        // Mesafe ve süre hesaplamasını yap
        this.calculateDistanceAndDuration(this.selectedAddress);
      } else {
        console.error('Adres geocode edilmedi:', status);
      }
    });
  } else {
    console.error("Geçersiz adres veya konum bilgisi.");
  }
},
onAddressSelected(location) {
  if (location && location.lat && location.lng) {
    this.selectedAddress = location;
    this.appointment.address = location.formatted_address; 
    this.calculateDistanceAndDuration(location);
    
    // Posta kodunu Google Geocoding API sonuçlarından ayıklayın
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: { lat: location.lat, lng: location.lng } }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const postcodeComponent = results[0].address_components.find(comp => comp.types.includes("postal_code"));
        if (postcodeComponent) {
          this.appointment.postcode = postcodeComponent.long_name; // Posta kodunu inputa yerleştirin
          this.addressSelectedFromMap = true; 
        }
      }
    });
  } else {
    console.error('Geçersiz konum bilgisi alındı.');
  }
},
async fetchPostcodeDetails() {
  if (this.appointment.postcode) {
    this.loadingAddresses = true;  
    try {
      const postcodeResponse = await axios.get(`http://localhost:8000/api/fetch-postcode-details/${this.appointment.postcode}`);
      

      if (postcodeResponse.data && postcodeResponse.data.result) {
        const latitude = postcodeResponse.data.result.latitude;
        const longitude = postcodeResponse.data.result.longitude;

        const googleResponse = await axios.get(`http://localhost:8000/api/fetch-nearby-addresses`, {
          params: {
            lat: latitude,
            lon: longitude
          }
        });

       

        if (googleResponse.data && googleResponse.data.results) {
          this.addresses = googleResponse.data.results.map(item => item.formatted_address);
        } else {
          this.addresses = [];
          console.warn('Yakınlarda adres bulunamadı veya response formatı beklenen gibi değil.');
        }
      } else {
        this.addresses = [];
        console.warn('Postcode için sonuç bulunamadı.');
      }
    } catch (error) {
      console.error('Adresler yüklenemedi:', error);
      this.addresses = []; 
    } finally {
      this.loadingAddresses = false;  
    }
  } else {
    this.addresses = []; 
  }
},

    updateTimes() {
      if (this.appointment.date && this.selectedAddress) {
        this.calculateDistanceAndDuration(this.selectedAddress);
      }
    },
    async calculateDistanceAndDuration(destination) {
      if (!destination || typeof destination.lat !== 'number' || typeof destination.lng !== 'number') {
        console.error('Geçersiz konum bilgisi:', destination);
        return;
      }

      const service = new google.maps.DistanceMatrixService();
      service.getDistanceMatrix(
        {
          origins: [this.officeLocation],
          destinations: [{ lat: Number(destination.lat), lng: Number(destination.lng) }],
          travelMode: 'DRIVING',
        },
        (response, status) => {
          if (status === 'OK' && response.rows[0].elements[0].status === 'OK') {
            this.distance = (response.rows[0].elements[0].distance.value / 1000).toFixed(2); // km
            this.travelDuration = response.rows[0].elements[0].duration.text; // sürüş süresi
            this.calculateTimes(response.rows[0].elements[0].duration.value); // süreyi saniye cinsinden alır
          } else {
            console.error('Mesafe veya süre hesaplanamadı:', status);
          }
        }
      );
    },
    calculateTimes(durationInSeconds) {
      const appointmentTime = new Date(this.appointment.date);
      if (isNaN(appointmentTime)) {
        console.error('Randevu tarihi geçerli değil.');
        return;
      }
      
      const appointmentDurationInSeconds = 3600; 

      const departureTime = new Date(appointmentTime.getTime() - durationInSeconds * 1000);
      this.estimatedDepartureTime = departureTime.toLocaleTimeString();

      const returnTime = new Date(appointmentTime.getTime() + (appointmentDurationInSeconds + durationInSeconds) * 1000);
      this.estimatedAvailableTime = returnTime.toLocaleTimeString();
    },
    async fetchEmployeeAppointments(employeeId) {
  try {
    const response = await axios.get(`http://localhost:8000/api/employees/${employeeId}/appointments`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error('Çalışanın randevuları yüklenemedi:', error);
    return [];
  }
},

  
  async checkAppointmentOverlap() {
  const appointments = await this.fetchEmployeeAppointments(this.appointment.employee.id);

  const newStartDate = this.appointment.date.split("T")[0]; // Tarih kısmını al
  const newStartDateTime = new Date(newStartDate + " " + this.estimatedDepartureTime); // Yeni randevu başlangıç tarihi ve saati
  const newEndDateTime = new Date(newStartDate + " " + this.estimatedAvailableTime); // Yeni randevu bitiş tarihi ve saati

  for (let appt of appointments) {
    const existingDate = appt.appointment_date.split(" ")[0]; // Var olan randevunun tarih kısmını al
    const existingStartTime = new Date(existingDate + " " + appt.departure_time); // Mevcut randevu başlangıç tarihi ve saati
    const existingEndTime = new Date(existingDate + " " + appt.available_time); // Mevcut randevu bitiş tarihi ve saati

    // Aynı tarihte mi diye kontrol et
    if (newStartDate === existingDate) {
      // Tarih ve saat karşılaştırmaları
      if (
          (newStartDateTime >= existingStartTime && newStartDateTime < existingEndTime) ||
          (newEndDateTime > existingStartTime && newEndDateTime <= existingEndTime) ||
          (existingStartTime >= newStartDateTime && existingStartTime < newEndDateTime)
      ) {
        return true; 
      }
    }
  }
  return false; 
},



  
  async submitForm() {


  try {
    if (!this.selectedAddress || typeof this.selectedAddress.lat !== 'number' || typeof this.selectedAddress.lng !== 'number') {
      alert('Lütfen geçerli bir adres seçin.');
      return;
    }

    // Çakışma kontrolü yap
    const hasConflict = await this.checkAppointmentOverlap();
    console.log(await this.checkAppointmentOverlap());
    console.log(this.appointment.date);
    
    
    if (hasConflict) {
      
      const result = await this.$swal.fire({
        title: 'Çakışma Tespit Edildi!',
        text: 'Bu çalışanın başka bir randevusu bu saat aralığında mevcut. Yine de devam etmek istiyor musunuz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Devam Et',
        cancelButtonText: 'Hayır, İptal Et'
      });

      if (!result.isConfirmed) {
        return; // Kullanıcı iptal ettiyse çık
      }
    }

    // Çakışma yoksa veya kullanıcı "Devam Et" dediyse randevuyu oluştur
    const response = await axios.post('http://localhost:8000/api/appointments', {
      postcode: this.appointment.postcode,
      appointment_date: this.appointment.date,
      client_name: this.appointment.client.name,
      client_email: this.appointment.client.email,
      client_phone: this.appointment.client.phone,
      employee_id: this.appointment.employee.id,
      location_lat: this.selectedAddress.lat,
      location_lng: this.selectedAddress.lng,
      distance: this.distance,
      duration: this.travelDuration,
      departure_time: this.estimatedDepartureTime,
      available_time: this.estimatedAvailableTime,
      address: this.appointment.address,
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });

    this.$swal.fire({
      title: 'Başarılı!',
      text: 'Randevu Başarıyla Oluşturuldu.',
      icon: 'success',
      confirmButtonText: 'Devam et'
    }).then(() => {
      this.$router.push('/appointments');
    });
  } catch (error) {
    if (error.response && error.response.data) {
      console.error('Backend Hatası:', error.response.data);
      this.$swal.fire({
        title: 'Hata!',
        text: 'Randevu Oluşturulamadı Lütfen Tekrar Deneyin.',
        icon: 'error',
        confirmButtonText: 'Devam et'
      });
    } else {
      console.error('Randevu oluşturulamadı:', error);
      this.$swal.fire({
        title: 'Hata!',
        text: 'Randevu Oluşturulamadı Lütfen Tekrar Deneyin.',
        icon: 'error',
        confirmButtonText: 'Tamam'
      });
    }
  }
}
,
    
    async fetchEmployees() {
      try {
        const response = await axios.get('http://localhost:8000/api/employees', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.employees = response.data;
      } catch (error) {
        console.error('Çalışanlar yüklenemedi:', error);
      }
    },
  },
  created() {
    this.fetchEmployees(); 
  },
};
</script>

<style scoped>
.appointment-form {
  margin-left: 250px;
    position: relative;
  margin-top: 20px;
    border-radius: 5px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.appointment-form h2 {
  font-size: 28px;
  margin-bottom: 20px;
}

.appointment-form form button:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
  color: #fff;
  opacity: 0.6;
}

.appointment-form form {
  background: #ffffff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 800px;
}

.appointment-form form div {
  margin-bottom: 15px;
}

.appointment-form form label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
.loader {
  margin-top: 10px;
  font-size: 14px;
  color: #3498db;
}
.appointment-form form input,
.appointment-form form select {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #bdc3c7;
  box-sizing: border-box;
}

.appointment-form form button {
  padding: 10px 20px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  width: 100%;
}

.appointment-form form button:hover {
  background-color: #2980b9;
}
@media screen and (max-width: 576px) {
.appointment-form {
  margin-left: 100px;
}
 }
</style>

