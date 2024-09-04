<template>
  <div class="employee-form">
    <h2>Çalışan Ekle</h2>
    <form @submit.prevent="addEmployee">
      <div>
        <label for="first-name">Ad:</label>
        <input v-model="firstName" id="first-name" required />
      </div>
      <div>
        <label for="last-name">Soyad:</label>
        <input v-model="lastName" id="last-name" required />
      </div>
      <div>
        <label for="email">E-posta:</label>
        <input v-model="email" type="email" id="email" required />
      </div>
      <div>
        <label for="password">Şifre:</label>
        <input v-model="password" type="password" id="password" required />
      </div>
      <button type="submit" :disabled="isFormInvalid">Çalışan Ekle</button>
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
      password: '',
    };
  },
  computed: {
    isFormInvalid() {
      return !this.firstName || !this.lastName || !this.email || !this.password;
    },
  },
  methods: {
    async addEmployee() {
      try {
        const response = await axios.post('http://localhost:8000/api/employees', {
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email,
          password: this.password,
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });

        alert('Çalışan başarıyla eklendi!');
        this.$router.push('/employees');
      } catch (error) {
        alert('Çalışan eklenemedi. Lütfen bilgileri kontrol edin.');
      }
    },
  },
};
</script>

<style scoped>
.employee-form {
  width: 400px;
  margin: 50px auto;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
}

.employee-form h2 {
  margin-bottom: 20px;
  text-align: center;
}

.employee-form form div {
  margin-bottom: 15px;
}

.employee-form label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.employee-form input {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

.employee-form button {
  width: 100%;
  padding: 10px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.employee-form button[disabled] {
  background-color: #ccc;
  cursor: not-allowed;
}

.employee-form button:hover:not([disabled]) {
  background-color: #0069d9;
}
</style>
