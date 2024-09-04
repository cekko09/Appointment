<template>
  <div class="appointment-form">
    <h2>Yeni Randevu Oluştur</h2>
    <form @submit.prevent="submitForm">
      <!-- Randevu Adresi Posta Kodu -->
      <div>
        <label for="address">Randevu Adresi Posta Kodu:</label>
        <input v-model="appointment.postcode" id="address" required />
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
      <Map @addressSelected="onAddressSelected" />

      <!-- Mesafe ve Süre Bilgileri -->
      <p>Ofis ile randevu adresi arası mesafe: {{ distance }} km</p>
      <p>Tahmini Seyahat Süresi: {{ travelDuration }} </p>
      <p>Ofisten Çıkış Zamanı: {{ estimatedDepartureTime }}</p>
      <p>Randevudan Sonra Müsait Olacağı Zaman: {{ estimatedAvailableTime }}</p>
      
      <!-- Randevu Oluşturma Butonu -->
      <button type="submit">Randevu Oluştur</button>
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
        location: null
      },
      employees: [], // Çalışanlar API'den çekilecek
      selectedAddress: null,
      distance: '0',
      travelDuration: '0',
      estimatedDepartureTime: '',
      estimatedAvailableTime: '',
      officeLocation: {
        lat: 51.5074, // Londra örnek konum
        lng: -0.1278,
      }
    };
  },
  methods: {
    // Haritadan seçilen adresi alır ve mesafe hesaplamalarını yapar
    onAddressSelected(location) {
      this.selectedAddress = location;
      this.calculateDistanceAndDuration(location);
    },
    
    // Randevu tarihi veya adres değiştiğinde tahmini zamanları güncelle
    updateTimes() {
      if (this.appointment.date && this.selectedAddress) {
        this.calculateDistanceAndDuration(this.selectedAddress);
      }
    },

    // Google Maps API kullanarak mesafe ve süre hesaplama
    async calculateDistanceAndDuration(destination) {
      const service = new google.maps.DistanceMatrixService();
      service.getDistanceMatrix(
        {
          origins: [this.officeLocation],
          destinations: [{ lat: destination.lat, lng: destination.lng }],
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

    // Tahmini çıkış ve müsait olacağı zamanı hesapla
    calculateTimes(durationInSeconds) {
      const appointmentTime = new Date(this.appointment.date);
      if (isNaN(appointmentTime)) {
        console.error('Randevu tarihi geçerli değil.');
        return;
      }
      
      // Randevu süresi: 1 saat
      const appointmentDurationInSeconds = 3600; // 1 saat = 3600 saniye

      // Ofisten çıkış zamanı (randevu yerine ulaşmak için)
      const departureTime = new Date(appointmentTime.getTime() - durationInSeconds * 1000);
      this.estimatedDepartureTime = departureTime.toLocaleTimeString();

      // Randevudan sonra dönüş zamanı
      const returnTime = new Date(appointmentTime.getTime() + (appointmentDurationInSeconds + durationInSeconds) * 1000);
      this.estimatedAvailableTime = returnTime.toLocaleTimeString();
    },

    // Randevu oluşturmak için form verilerini gönderme
   // Randevu oluşturmak için form verilerini gönderme
   async submitForm() {
      try {
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
          departure_time: this.estimatedDepartureTime, // Ofisten çıkış zamanı gönderiliyor
          available_time: this.estimatedAvailableTime, // Müsait olacağı zaman gönderiliyor
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });

        console.log('Randevu başarıyla oluşturuldu:', response.data);
        alert('Randevu başarıyla oluşturuldu!');
      } catch (error) {
        if (error.response && error.response.data) {
          console.error('Backend Hatası:', error.response.data);
          alert(`Hata: ${JSON.stringify(error.response.data.errors)}`);
        } else {
          console.error('Randevu oluşturulamadı:', error);
          alert('Randevu oluşturulamadı. Lütfen tekrar deneyin.');
        }
      }
    },
    // Çalışanları API'den çeker
    async fetchEmployees() {
      try {
        const response = await axios.get('http://localhost:8000/api/employees', { // employees endpoint'ini kullan
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.employees = response.data;
      } catch (error) {
        console.error('Çalışanlar yüklenemedi:', error);
      }
    },
    
    // Harita script'ini yükler ve haritayı başlatır
    loadMapScript() {
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAunuRDlZ1mHwkhG0a_9YoEIfyScIQC5jo&libraries=places`;
      script.async = true;
      script.defer = true;
      script.onload = this.initMap;
      document.head.appendChild(script);
    }
  },
  created() {
    this.fetchEmployees(); // Çalışanları çekmek için API çağrısı
  },
  mounted() {
  },
};
</script>

<style scoped>
.appointment-form {
  width: 70%;
  margin: auto;
  padding: 20px;
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

/* Mobil uyum için düzen */
@media (max-width: 768px) {
  .appointment-form {
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
</style>
