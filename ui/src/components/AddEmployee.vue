<template>
    <div class="add-employee-container">
      <h2>Çalışan Ekle</h2>
      <form @submit.prevent="addEmployee" class="employee-form">
        <input v-model="firstName" type="text" placeholder="Adı" required />
        <input v-model="lastName" type="text" placeholder="Soyadı" required />
        <input v-model="email" type="email" placeholder="E-posta" required />
        <button type="submit" class="submit-button">Ekle</button>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        firstName: '',
        lastName: '',
        email: '',
      };
    },
    methods: {
      async addEmployee() {
        try {
          await axios.post('http://localhost:8000/api/employees', {
            first_name: this.firstName,
            last_name: this.lastName,
            email: this.email,
          }, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          });
          alert('Çalışan eklendi!');
          this.firstName = '';
          this.lastName = '';
          this.email = '';
        } catch (error) {
          console.error('Çalışan eklenemedi:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .add-employee-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  
  .employee-form {
    display: flex;
    flex-direction: column;
  }
  
  .employee-form input {
    margin-bottom: 15px;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .submit-button {
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
  }
  
  .submit-button:hover {
    background-color: #45a049;
  }
  </style>
  