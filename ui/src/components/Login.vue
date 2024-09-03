<template>
  <div class="login-container">
    <h2 class="login-title">Süperadmin Girişi</h2>
    <form @submit.prevent="login" class="login-form">
      <div class="form-group">
        <label for="email">Email</label>
        <input v-model="email" type="email" id="email" required />
      </div>
      <div class="form-group">
        <label for="password">Parola</label>
        <input v-model="password" type="password" id="password" required />
      </div>
      <button type="submit" class="login-button">Giriş Yap</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('http://localhost:8000/api/login', {
          email: this.email,
          password: this.password,
        });

        const { token, user } = response.data;
        localStorage.setItem('token', token);
        localStorage.setItem('userRole', user.role);

        if (user.role === 'employee') {
          this.$router.push('/appointments');
        } else {
          this.$router.push('/dashboard');
        }
      } catch (error) {
        console.error('Giriş hatası:', error.response?.data);
        alert('Giriş başarısız. Lütfen bilgilerinizi kontrol edin.');
      }
    },
  },
};
</script>


  <style scoped>
  .login-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
  }
  
  .login-title {
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
  }
  
  .login-form .form-group {
    margin-bottom: 15px;
  }
  
  .login-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  .login-form input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
  }
  
  .login-button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 18px;
    cursor: pointer;
  }
  
  .login-button:hover {
    background-color: #0056b3;
  }
  </style>
  