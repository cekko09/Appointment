<template>
  <div class="appointment-form">
    <h2>Randevu Düzenle</h2>
    <form @submit.prevent="updateAppointment">
      <!-- Randevu Adresi Posta Kodu -->
      <div>
        <label for="address">Randevu Adresi Posta Kodu:</label>
        <input v-model="appointment.postcode" id="address" required />
      </div>

      <!-- Randevu Tarihi -->
      <div>
        <label for="date">Randevu Tarihi:</label>
        <input type="datetime-local" v-model="appointment.appointment_date" id="date" required />
      </div>

      <!-- Müşteri Bilgileri -->
      <div>
        <label for="client-name">Müşteri Adı:</label>
        <input v-model="appointment.client_name" id="client-name" required />
      </div>
      <div>
        <label for="client-email">Müşteri Email:</label>
        <input v-model="appointment.client_email" id="client-email" required />
      </div>
      <div>
        <label for="client-phone">Müşteri Telefon:</label>
        <input v-model="appointment.client_phone" id="client-phone" required />
      </div>

      <!-- Emlakçı Çalışanı Seçimi -->
      <div>
        <label for="employee">Emlakçı Çalışanı:</label>
        <select v-model="appointment.employee_id" id="employee" required>
          <option v-for="employee in employees" :key="employee.id" :value="employee.id">
            {{ employee.first_name }} {{ employee.last_name }}
          </option>
        </select>
      </div>

      <!-- Harita Bileşeni -->
      <Map @addressSelected="onAddressSelected" />

      <!-- Mesafe ve Süre Bilgileri -->
      <p>Ofis ile randevu adresi arası mesafe: {{ appointment.distance }} km</p>
      <p>Tahmini Seyahat Süresi: {{ appointment.duration }} </p>
      <p>Ofisten Çıkış Zamanı: {{ appointment.departure_time }}</p>
      <p>Randevudan Sonra Müsait Olacağı Zaman: {{ appointment.available_time }}</p>
      
      <!-- Randevu Güncelleme Butonu -->
      <button type="submit">Randevuyu Güncelle</button>
    </form>
  </div>
</template>

<script>
import Map from './Map.vue';
import axios from 'axios';

export default {
  name: 'EditAppointment',
  components: {
    Map,
  },
  data() {
    return {
      appointment: {
        postcode: '',
        appointment_date: '',
        client_name: '',
        client_email: '',
        client_phone: '',
        employee_id: null,
        location_lat: null,
        location_lng: null,
        distance: '',
        duration: '',
        departure_time: '',
        available_time: ''
      },
      employees: [],
      officeLocation: { lat: 51.5074, lng: -0.1278 }, // Ofis konumu örneği
      selectedAddress: null,
    };
  },
  methods: {
    async fetchAppointment() {
      const route = this.$route;
      try {
        const response = await axios.get(`http://localhost:8000/api/appointments/${route.params.id}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.appointment = response.data;
      } catch (error) {
        console.error('Randevu verisi yüklenemedi:', error);
      }
    },
    onAddressSelected(location) {
      this.selectedAddress = location;  // Yeni konumu kaydet
      this.appointment.location_lat = location.lat;
      this.appointment.location_lng = location.lng;
      this.updateTimes();  // Yeni konum seçildiğinde zamanları güncelle
    },
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
            this.appointment.distance = (response.rows[0].elements[0].distance.value / 1000).toFixed(2); // km
            this.appointment.duration = response.rows[0].elements[0].duration.text; // sürüş süresi
            this.calculateTimes(response.rows[0].elements[0].duration.value); // süreyi saniye cinsinden alır
          } else {
            console.error('Mesafe veya süre hesaplanamadı:', status);
          }
        }
      );
    },
    calculateTimes(durationInSeconds) {
      const appointmentTime = new Date(this.appointment.appointment_date);
      const appointmentDurationInSeconds = 3600; // 1 saat = 3600 saniye

      // Ofisten çıkış zamanı
      const departureTime = new Date(appointmentTime.getTime() - durationInSeconds * 1000);
      this.appointment.departure_time = departureTime.toLocaleTimeString();

      // Randevudan sonra dönüş zamanı
      const returnTime = new Date(appointmentTime.getTime() + (appointmentDurationInSeconds + durationInSeconds) * 1000);
      this.appointment.available_time = returnTime.toLocaleTimeString();
    },
    updateTimes() {
      if (this.appointment.appointment_date && this.selectedAddress) {
        this.calculateDistanceAndDuration(this.selectedAddress);
      }
    },
    // Randevuyu güncelleme metodu
    async updateAppointment() {
      try {
        const response = await axios.put(`http://localhost:8000/api/appointments/${this.appointment.id}`, this.appointment, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        console.log('Randevu başarıyla güncellendi:', response.data);
        alert('Randevu başarıyla güncellendi!');
        this.$router.push('/appointments');
      } catch (error) {
        console.error('Randevu güncellenemedi:', error);
        alert('Randevu güncellenemedi. Lütfen tekrar deneyin.');
      }
    },
    // Çalışanları API'den çek
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
  watch: {
    'appointment.appointment_date': 'updateTimes',  // Tarih değiştiğinde zamanları güncelle
  },
  created() {
    this.fetchEmployees();
    this.fetchAppointment();
  },
};
</script>

<style scoped>
.appointment-form {
  width: 90%;
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