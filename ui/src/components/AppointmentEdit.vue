<template>
  <div class="appointment-form">
    <h2>Randevu Düzenle</h2>
    <Form @submit="updateAppointment" :validation-schema="validationSchema">
      <div>
        <label for="postcode">Randevu Adresi Posta Kodu:</label>
        <Field v-model="appointment.postcode" name="postcode" id="postcode" />
        <ErrorMessage name="postcode" />
      </div>

      <div v-if="addresses.length === 0">
        <label for="address">Randevu Adresi:</label>
        <Field v-model="appointment.address" name="address" id="address" readonly />
        <ErrorMessage name="address" />
      </div>
      <div v-else-if="addresses.length > 0 && appointment.postcode && !addressSelectedFromMap">
        <label for="address-select">Adres Seç:</label>
        <Field as="select" v-model="appointment.address" name="address">
          <option v-for="(address, index) in addresses" :key="index" :value="address">{{ address }}</option>
        </Field>
        <ErrorMessage name="address" />
      </div>

      <div>
        <label for="date">Randevu Tarihi:</label>
        <Field type="datetime-local" v-model="appointment.appointment_date" name="appointment_date" id="date" />
        <ErrorMessage name="appointment_date" />
      </div>

      <div>
        <label for="client-name">Müşteri Adı:</label>
        <Field v-model="appointment.client_name" name="client_name" id="client-name" />
        <ErrorMessage name="client_name" />
      </div>
      <div>
        <label for="client-email">Müşteri Email:</label>
        <Field v-model="appointment.client_email" name="client_email" id="client-email" />
        <ErrorMessage name="client_email" />
      </div>
      <div>
        <label for="client-phone">Müşteri Telefon:</label>
        <Field v-model="appointment.client_phone" name="client_phone" id="client-phone" />
        <ErrorMessage name="client_phone" />
      </div>

      <div>
        <label for="employee">Emlakçı Çalışanı:</label>
        <Field as="select" v-model="appointment.employee_id" name="employee_id" id="employee">
          <option v-for="employee in employees" :key="employee.id" :value="employee.id">
            {{ employee.first_name }} {{ employee.last_name }}
          </option>
        </Field>
        <ErrorMessage name="employee_id" />
      </div>

      <Map ref="mapComponent" @addressSelected="onAddressSelected" />

      <p>Ofis ile randevu adresi arası mesafe: {{ appointment.distance }} km</p>
      <p>Tahmini Seyahat Süresi: {{ appointment.duration }}</p>
      <p>Ofisten Çıkış Zamanı: {{ appointment.departure_time }}</p>
      <p>Randevudan Sonra Müsait Olacağı Zaman: {{ appointment.available_time }}</p>

      <button :disabled="!isFormValid" type="submit">Randevuyu Güncelle</button>
    </Form>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { useForm, Field, Form, ErrorMessage } from 'vee-validate';
import { validationSchema } from '@/validations/validationSchema';
import Map from './Map.vue';
import axios from 'axios';

export default defineComponent({
  name: 'AppointmentEdit',
  components: { Map },
  setup() {
    const { resetForm, handleSubmit, errors, values, setValues, setFieldValue, validateForm } = useForm({
      validationSchema,
    });

    const appointment = ref({
      postcode: '',
      appointment_date: '',
      client_name: '',
      client_email: '',
      client_phone: '',
      employee_id: null,
      address: '',
      location_lat: null,
      location_lng: null,
      distance: '0',
      duration: '0',
      departure_time: '',
      available_time: '',
    });

    const employees = ref([]);
    const addresses = ref([]);
    const addressSelectedFromMap = ref(false);

    const fetchAppointment = async () => {
      const route = this.$route;
      try {
        const response = await axios.get(`http://localhost:8000/api/appointments/${route.params.id}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        setValues(response.data);
        // Diğer işlemler
      } catch (error) {
        console.error('Randevu verisi yüklenemedi:', error);
      }
    };

    const fetchEmployees = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/employees', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        employees.value = response.data;
      } catch (error) {
        console.error('Çalışanlar yüklenemedi:', error);
      }
    };

    const updateAppointment = handleSubmit(async () => {
      try {
        const route = this.$route;
        await axios.put(`http://localhost:8000/api/appointments/${route.params.id}`, values, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        alert('Randevu başarıyla güncellendi.');
        this.$router.push('/appointments');
      } catch (error) {
        console.error('Randevu güncellenirken hata oluştu:', error);
        alert('Randevu güncellenemedi. Lütfen tekrar deneyin.');
      }
    });

    onMounted(() => {
      fetchAppointment();
      fetchEmployees();
    });

    return {
      appointment,
      employees,
      addresses,
      addressSelectedFromMap,
      validationSchema,
      updateAppointment,
      errors,
      values,
      setValues,
      setFieldValue,
      validateForm,
    };
  },
  methods: {
    onSubmit(values) {
      console.log('Form submitted with:', values);
      // Randevuyu güncelleme işlemi burada yapılacak.
    },
  },
  created() {
    this.fetchEmployees();
  },
});
</script>




<style scoped>
.appointment-form {
  margin: 20px auto;
  width: 90%;
  padding: 20px;
  background-color: #ecf0f1;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  border-radius: 5px;
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


