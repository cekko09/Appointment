
<template>
  <div class="emp_list_container">
  <div class="employee-form">
    <h2>Çalışan Ekle</h2>
    <Form  @submit="addEmployee" v-slot="{ isSubmitting, meta }">
      <div>
        <label for="first-name">Ad:</label>
        <Field name="firstName" id="first-name" rules="required" v-slot="{ field, errors, meta }">
          <input v-bind="field" v-model="firstName" :class="{ dirty: meta.dirty, touched: meta.touched && errors.length > 0 }" required />
          <span v-if="errors.length">{{ errors[0] }}</span>
        </Field>
      </div>
      <div>
        <label for="last-name">Soyad:</label>
        <Field name="lastName" id="last-name" rules="required" v-slot="{ field, errors, meta }">
          <input v-bind="field" v-model="lastName" :class="{ dirty: meta.dirty, touched: meta.touched && errors.length > 0 }" required />
          <span v-if="errors.length">{{ errors[0] }}</span>
        </Field>
      </div>
      <div>
        <label for="email">E-posta:</label>
        <Field name="email" id="email" rules="required|email" v-slot="{ field, errors, meta }">
          <input type="email" v-model="email" v-bind="field" :class="{ dirty: meta.dirty, touched: meta.touched && errors.length > 0}" required />
          <span v-if="errors.length">{{ errors[0] }}</span>
        </Field>
      </div>
      <div>
        <label for="password">Şifre:</label>
        <Field name="password"  id="password" rules="required|min:8" v-slot="{ field, errors, meta }">
          <input type="password" v-model="password" v-bind="field" :class="{ dirty: meta.dirty, touched: meta.touched && errors.length > 0 }" required />
          <span v-if="errors.length">{{ errors[0] }}</span>
        </Field>
      </div>
      <button type="submit" :disabled="!meta.valid || isSubmitting">Çalışan Ekle</button>
    </Form>
  </div>
</div>
</template>

<script>
import axios from 'axios';
import { Form, Field, defineRule, ErrorMessage } from 'vee-validate';
import { required, email, min } from '@vee-validate/rules';

defineRule('required', required);
defineRule('email', email);
defineRule('min', min);

export default {
  components: {
    Form,
    Field,
    ErrorMessage,
  },
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
        await axios.post('http://localhost:8000/api/employees', {
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email,
          password: this.password,
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
      
        this.$swal.fire({
          title: 'Başarılı!',
          text: 'Başarıyla eklendi',
          icon: 'success',
          confirmButtonText: 'Tamam'
        });
        this.$router.push('/employees');
      } catch (error) {
      if (error.response.data.errors.email) {
        this.$swal.fire({
          title: 'Hata!',
          text: 'Bu e-posta adresi zaten kayıtlı.',
          icon: 'error',
          confirmButtonText: 'Tamam'
        });
      }else if(error.response.data.errors.password){
        this.$swal.fire({
          title: 'Hata!',
          text: 'Şifre En az 8 karakterden oluşmalıdır',
          icon: 'error',
          confirmButtonText: 'Tamam'
        });
      }
       else  {
        console.log(error.response.data.errors.email)
        
        this.$swal.fire({
          title: 'Hata!',
          text: 'Çalışan eklenemedi. Lütfen tekrar deneyin.',
          icon: 'error',
          confirmButtonText: 'Tamam'
        });
      }
    }
    },
  },
};
</script>

<style scoped>
.emp_list_container {
  display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.employee-form {
  margin-left: 250px;
    position: relative;
    top: 20% !important;
   
    transform: translateY(10%);
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
  width: 50%;
  
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
.employee-form input.dirty {
  border-color: #007bff;
}

.employee-form input.touched {
  border-color: #e74c3c;
}
@media screen and (max-width: 576px) {
.employee-form {
  margin-left: 100px;
    width: 70%;
}


}
</style>
