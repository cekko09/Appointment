<template>
    <div class="appointment-edit">
      <h2>Randevuyu Düzenle</h2>
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
          <select v-model="appointment.user" id="user" required>
            <option v-for="user in users" :key="user.id" :value="user">{{ user.name }}</option>
          </select>
        </div>
        <Map @locationSelected="setLocation" :initialLocation="appointment.location" />
        <button type="submit">Randevuyu Güncelle</button>
      </form>
    </div>
  </template>
  
  <script>
  import Map from './Map.vue';
  
  export default {
    name: 'AppointmentEdit',
    components: { Map },
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
        users: [] // Kullanıcılar API'den çekilecek
      };
    },
    methods: {
      setLocation(location) {
        this.appointment.location = location;
        // Mesafe hesaplaması yapılabilir
      },
      submitForm() {
        // Güncelleme işlemi API çağrısı ile yapılabilir
      },
      loadAppointmentData() {
        const id = this.$route.params.id;
        // Burada API çağrısı ile randevu verileri yüklenebilir
        // Örnek veri:
        this.appointment = {
          postcode: 'cm27pj',
          date: '2024-08-30T10:00',
          client: {
            name: 'Ahmet Yılmaz',
            email: 'ahmet@example.com',
            phone: '555-1234'
          },
          user: this.users[0],
          location: { lat: 51.5074, lng: -0.1278 } // Örnek konum
        };
      }
    },
    created() {
      // API çağrıları burada yapılabilir, kullanıcıları ve randevu verilerini yükleyin
      this.loadAppointmentData();
    }
  };
  </script>
  
  <style scoped>
  .appointment-edit {
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
  
  .appointment-edit h2 {
    font-size: 28px;
    margin-bottom: 20px;
  }
  
  .appointment-edit form {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
  }
  
  .appointment-edit form div {
    margin-bottom: 15px;
  }
  
  .appointment-edit form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  .appointment-edit form input,
  .appointment-edit form select {
    width: 100%;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #bdc3c7;
    box-sizing: border-box;
  }
  
  .appointment-edit form button {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
  }
  
  .appointment-edit form button:hover {
    background-color: #2980b9;
  }
  
  /* Mobil uyum için düzen */
  @media (max-width: 768px) {
    .appointment-edit {
      margin-left: 0;
      padding: 15px;
    }
  
    .appointment-edit h2 {
      font-size: 24px;
    }
  
    .appointment-edit form {
      padding: 15px;
    }
  
    .appointment-edit form button {
      font-size: 18px;
      padding: 12px;
    }
  }
  </style>