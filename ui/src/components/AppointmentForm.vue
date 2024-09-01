<template>
  <div class="appointment-form">
    <h2>Yeni Randevu Oluştur</h2>
    <form @submit.prevent="submitForm">
      <div>
        <label for="address">Randevu Adresi Posta Kodu:</label>
        <input v-model="appointment.postcode" id="address" required />
      </div>
      <div>
        <label for="date">Randevu Tarihi:</label>
        <input type="datetime-local" v-model="appointment.date" id="date" required />
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
        <label for="user">Emlakçı Çalışanı:</label>
        <select v-model="appointment.user" id="user" >
          <option v-for="user in users" :key="user.id" :value="user">{{ user.name }}</option>
        </select>
      </div>
      
      <Map @addressSelected="onAddressSelected" />

      <p>Ofis ile randevu adresi arası mesafe: {{ distance }} km</p>
      <p>Tahmini Seyahat Süresi: {{ duration }}</p>
      <button type="submit">Randevu Oluştur</button>
    </form>
  </div>
</template>

<script>
import Map from './Map.vue';

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
        user: null,
        location: null
      },
      users: [], // Kullanıcılar API'den çekilecek
      selectedAddress: null,
      distance: '0',
      duration: '0',
      officeLocation: {
        lat: 51.5074, // Londra (Örnek)
        lng: -0.1278,
      }
    };
  },
  methods: {
    onAddressSelected(location) {
      this.selectedAddress = location;
      this.calculateDistanceAndDuration(location);
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
            this.distance = (response.rows[0].elements[0].distance.value / 1000).toFixed(2); // km
            this.duration = response.rows[0].elements[0].duration.text; // dakika cinsinden süre
          } else {
            console.error('Mesafe veya süre hesaplanamadı:', status);
          }
        }
      );
    },
    submitForm() {
      // Form gönderme işlemleri burada yapılacak.
      console.log("Form verileri: ", this.appointment, this.distance);
    }
  },
  created() {
    // API çağrıları burada yapılabilir
  }
};
</script>

<style scoped>
.appointment-form {
  margin-left: 260px;
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
