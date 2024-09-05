<template>
  <div class="appointment-form">
    <h2>Yeni Randevu Oluştur</h2>
    <form @submit.prevent="submitForm">
      <!-- Randevu Adresi Posta Kodu -->
      <div>
        <label for="postcode">Randevu Adresi Posta Kodu:</label>
        <input v-model="appointment.postcode" id="postcode" @input="fetchPostcodeDetails" />
      </div>

      <!-- Adres Input veya Selectbox -->
      <div v-if="addresses.length === 0">
        <label for="address">Randevu Adresi:</label>
        <input v-model="appointment.address" id="address" readonly />
      </div>
      <div v-else-if="addresses.length > 0 && appointment.postcode && !addressSelectedFromMap">
  <label for="address-select">Adres Seç:</label>
  <select v-model="appointment.address" id="address-select" @change="handleAddressChange">
    <option v-for="(address, index) in addresses" :key="index" :value="address">{{ address }}</option>
  </select>
</div>

      <!-- Randevu Tarihi -->
      <div>
        <label for="date">Randevu Tarihi:</label>
        <input type="datetime-local" v-model="appointment.date" @change="updateTimes" id="date" required />
      </div>

      <!-- Müşteri Bilgileri -->
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

      <!-- Emlakçı Çalışanı Seçimi -->
      <div>
        <label for="employee">Emlakçı Çalışanı:</label>
        <select v-model="appointment.employee" id="employee" required>
          <option v-for="employee in employees" :key="employee.id" :value="employee">{{ employee.first_name }} {{ employee.last_name }}</option>
        </select>
      </div>

      <!-- Harita Bileşeni -->
      <Map ref="mapComponent" @addressSelected="onAddressSelected" />

      <!-- Mesafe ve Süre Bilgileri -->
      <p>Ofis ile randevu adresi arası mesafe: {{ distance }} km</p>
      <p>Tahmini Seyahat Süresi: {{ travelDuration }} </p>
      <p>Ofisten Çıkış Zamanı: {{ estimatedDepartureTime }}</p>
      <p>Randevudan Sonra Müsait Olacağı Zaman: {{ estimatedAvailableTime }}</p>
      
      <!-- Randevu Oluşturma Butonu -->
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
      addressSelectedFromMap: false
    };
  },
  watch: {
    'appointment.postcode': function(newVal) {
      if (!newVal) {
        this.appointment.address = ''; // Posta kodu silindiğinde adresi sıfırlayın
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
    this.selectAddress(event);  // İlk metodu çağır
    this.updateTimes();  // İkinci metodu çağır
  },
  selectAddress() {
  const selected = this.appointment.address;
  if (selected) {
    // Adresin enlem ve boylamını almak için Google Geocoding API'sini kullanın
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
    this.appointment.address = location.formatted_address; // Adres inputunu güncelle
    this.calculateDistanceAndDuration(location);
    
    // Posta kodunu Google Geocoding API sonuçlarından ayıklayın
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: { lat: location.lat, lng: location.lng } }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const postcodeComponent = results[0].address_components.find(comp => comp.types.includes("postal_code"));
        if (postcodeComponent) {
          this.appointment.postcode = postcodeComponent.long_name; // Posta kodunu inputa yerleştirin
          this.addressSelectedFromMap = true; // Adres haritadan seçildi
        }
      }
    });
  } else {
    console.error('Geçersiz konum bilgisi alındı.');
  }
},
    async fetchPostcodeDetails() {
      if (this.appointment.postcode) {
        try {
          const postcodeResponse = await axios.get(`http://localhost:8000/api/fetch-postcode-details/${this.appointment.postcode}`);
          console.log('Postcode API Response:', postcodeResponse.data); // Debug için eklendi
          
          if (postcodeResponse.data && postcodeResponse.data.result) {
            const latitude = postcodeResponse.data.result.latitude;
            const longitude = postcodeResponse.data.result.longitude;

            const googleResponse = await axios.get(`http://localhost:8000/api/fetch-nearby-addresses`, {
              params: {
                lat: latitude,
                lon: longitude
              }
            });

            console.log('Google Maps API Response:', googleResponse.data); // Debug için eklendi

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
          this.addresses = []; // Hata durumunda adres listesini sıfırla
        }
      } else {
        this.addresses = []; // Posta kodu boşsa adres listesini sıfırla
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
      
      const appointmentDurationInSeconds = 3600; // 1 saat = 3600 saniye

      const departureTime = new Date(appointmentTime.getTime() - durationInSeconds * 1000);
      this.estimatedDepartureTime = departureTime.toLocaleTimeString();

      const returnTime = new Date(appointmentTime.getTime() + (appointmentDurationInSeconds + durationInSeconds) * 1000);
      this.estimatedAvailableTime = returnTime.toLocaleTimeString();
    },
    async submitForm() {
      console.log(this.appointment.address);  // Debug: Adres kontrolü
      console.log(this.appointment.postcode); // Debug: Posta kodu kontrolü

      try {
        if (!this.selectedAddress || typeof this.selectedAddress.lat !== 'number' || typeof this.selectedAddress.lng !== 'number') {
          alert('Lütfen geçerli bir adres seçin.');
          return;
        }
        
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
    },
    // Çalışanları API'den çeker
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
    this.fetchEmployees(); // Çalışanları çekmek için API çağrısı
  },
};
</script>

<style scoped>
.appointment-form {
  width: 60%;
  position: absolute;
  right: 5%;
  margin: 20px auto;
  padding: 20px;
  border-radius: 5px;
  background-color: #ecf0f1;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
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

/* 768px ve altı için stil */
@media (max-width: 768px) {
  .appointment-form {
    width: 80%;
    right: 30px;
    position: absolute;
    margin-left: 0;
    padding: 15px;
  }

  .appointment-form h2 {
    font-size: 24px;
  }

  .appointment-form form {
    padding: 15px;
  }

  .appointment-form form button {
    font-size: 18px;
    padding: 12px;
  }
}

/* 480px ve altı için stil */
@media (max-width: 480px) {
  .appointment-form {
    width: 80%;
    right: 10px;
    position: absolute;
    padding: 10px;
  }

  .appointment-form h2 {
    font-size: 20px;
    margin-bottom: 15px;
  }

  .appointment-form form {
    padding: 10px;
  }

  .appointment-form form input,
  .appointment-form form select {
    padding: 8px;
    font-size: 14px;
  }

  .appointment-form form button {
    font-size: 16px;
    padding: 10px;
  }
}
</style>

