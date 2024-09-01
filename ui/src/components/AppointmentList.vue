<template>
    <div class="appointment-list">
      <h2>Randevular</h2>
      <div>
        <label for="filter">Emlakçı Çalışanı:</label>
        <select v-model="selectedUser">
          <option v-for="user in users" :key="user.id" :value="user">{{ user.email }}</option>
        </select>
      </div>
      <ul>
        <li v-for="appointment in filteredAppointments" :key="appointment.id">
          <span>{{ appointment.date }} - {{ appointment.client.name }}</span>
          <button @click="editAppointment(appointment.id)">Düzenle</button>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    name: 'AppointmentList',
    data() {
      return {
        selectedUser: null,
        users: [], // Kullanıcıları API'den çekeceğiz
        appointments: []
      };
    },
    computed: {
      filteredAppointments() {
        if (!this.selectedUser) return this.appointments;
        return this.appointments.filter(a => a.user.id === this.selectedUser.id);
      }
    },
    async mounted() {
       
        try {
          const response = await axios.get('http://localhost:8000/api/employees');
          this.users = response.data;
          console.log(this.users);
          
        } catch (error) {
          console.error(error);
        }
    },
    methods: {
      editAppointment(id) {
        this.$router.push(`/appointments/edit/${id}`);
      }
    },
    created() {
      // API çağrıları burada yapılabilir
    }
  };
  </script>
  
  
  <style scoped>
  .appointment-list {
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
  
  .appointment-list h2 {
    font-size: 28px;
    margin-bottom: 20px;
  }
  
  .appointment-list ul {
    list-style-type: none;
    padding: 0;
    width: 100%;
    max-width: 1200px;
  }
  
  .appointment-list li {
    background: #ffffff;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  
  .appointment-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .appointment-details h3 {
    font-size: 20px;
    margin: 0;
  }
  
  .appointment-details p {
    margin: 0;
    color: #7f8c8d;
  }
  
  .appointment-details a {
    color: #3498db;
    text-decoration: none;
    font-weight: bold;
  }
  
  .appointment-details a:hover {
    text-decoration: underline;
  }
  
  /* Mobil uyum için düzen */
  @media (max-width: 768px) {
    .appointment-list {
      margin-left: 0;
      padding: 15px;
    }
  
    .appointment-list h2 {
      font-size: 24px;
    }
  
    .appointment-details {
      flex-direction: column;
      align-items: flex-start;
    }
  
    .appointment-details h3 {
      font-size: 18px;
    }
  
    .appointment-details p {
      font-size: 16px;
    }
  
    .appointment-details a {
      margin-top: 10px;
      font-size: 16px;
    }
  }
  </style>