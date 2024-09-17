<template>
  <div class="employee-container">
    <div class="employee-list-container">
      <h2>Çalışanlar Listesi</h2>
      <ul class="employee-list" >
        <li v-for="employee in employees" :key="employee.id" class="employee-item" >
          {{ employee.first_name }} {{ employee.last_name }} - {{ employee.email }}
         <div>
          <button @click="editEmployee(employee)" class="edit-button">Düzenle</button>
          <button @click="deleteEmployee(employee.id)" class="delete-button">Sil</button>
         </div>
        </li>
      </ul>
  
      <!-- Düzenleme Formu -->
      <div v-if="selectedEmployee" class="edit-form-container">
        <h3>Çalışanı Düzenle</h3>
        <Form @submit="updateEmployee" v-slot="{  meta }">
        <div class="employee-form">
          <Field name="first_name" rules="required" v-model="selectedEmployee.first_name" v-slot="{ field, errors, meta }">
            <input v-bind="field" type="text" placeholder="Adı" :class="{ touched: meta.touched && errors.length }" />
            <span v-if="errors[0]">{{ errors[0] }}</span>
          </Field>

          <Field name="last_name" rules="required" v-model="selectedEmployee.last_name" v-slot="{ field, errors, meta }">
            <input v-bind="field" type="text" placeholder="Soyadı" :class="{ touched: meta.touched && errors.length }" />
            <span v-if="errors[0]">{{ errors[0] }}</span>
          </Field>

          <Field name="email" rules="required|email" v-model="selectedEmployee.user.email" v-slot="{ field, errors, meta }">
            <input v-bind="field" type="email" placeholder="E-posta" :class="{ touched: meta.touched && errors.length }" />
            <span v-if="errors[0]">{{ errors[0] }}</span>
          </Field>

          <!-- Kaydet Butonu Yalnızca Alanlar Değiştiğinde Aktif -->
          <button type="submit" class="submit-button" :disabled="!meta.dirty">Kaydet</button>
          <button @click="cancelEdit" type="button" class="cancel-button">İptal</button>
        </div>
      </Form>
      </div>
  
      <!-- Geri Bildirim Mesajları -->
      <div v-if="message" :class="`alert ${messageType}`">
        {{ message }}
      </div>
  
      <!-- Yüklenme Göstergesi -->
      <div v-if="loading" class="loading">
        Yükleniyor...
      </div>
    </div>
  </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { ref, onMounted } from 'vue';
  import Swal from 'sweetalert2';
  import { Form, Field, defineRule } from 'vee-validate';
import { required, email } from '@vee-validate/rules';

// Vee-validate kuralları tanımlama
defineRule('required', required);
defineRule('email', email);

export default {
  components: {
    Form,
    Field,
  },
    setup() {
      const employees = ref([]);
      const selectedEmployee = ref(null);
      const message = ref('');
      const messageType = ref('');
      const loading = ref(false);
  
      const fetchEmployees = async () => {
  loading.value = true;
  try {
    const response = await axios.get('http://localhost:8000/api/employees', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });

    // Admin rolü olmayan kullanıcıları filtrele
    employees.value = response.data.filter(user => user.user.role !== 'admin');
    console.log(response.data);
    
    message.value = ''; // Önceki mesajı temizle
  } catch (error) {
    message.value = 'Çalışanlar yüklenemedi.';
    messageType.value = 'error';
  } finally {
    loading.value = false;
  }
};
  
      const deleteEmployee = async (id) => {
  // SweetAlert2 ile silme işlemi için onay kutusu
  const result = await Swal.fire({
    title: 'Emin misiniz?',
    text: 'Bu çalışanı silmek istediğinizden emin misiniz?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Evet, sil!',
    cancelButtonText: 'Hayır, iptal et'
  });


  if (!result.isConfirmed) return; // Eğer kullanıcı 'Hayır, iptal et' derse işlemi sonlandır

  loading.value = true;

  try {
    await axios.delete(`http://localhost:8000/api/employees/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });

    // Çalışanı listeden çıkar
    employees.value = employees.value.filter(employee => employee.id !== id);

    // Başarı mesajı
    console.log('BASARİ');
    
    Swal.fire({
      title: 'Başarılı!',
      text: 'Çalışan başarıyla silindi.',
      icon: 'success',
      confirmButtonText: 'Tamam'
    });
  } catch (error) {
    // Hata mesajı
    Swal.fire({
      title: 'Hata!',
      text: 'Çalışan silinemedi. Lütfen tekrar deneyin.',
      icon: 'error',
      confirmButtonText: 'Tamam'
    });
  } finally {
    loading.value = false;
  }
};

      const editEmployee = (employee) => {
        selectedEmployee.value = { ...employee }; // Seçili çalışanı düzenleme moduna al
        message.value = ''; // Mesajı temizle
      };
  
      const cancelEdit = () => {
        selectedEmployee.value = null; // Düzenleme işlemini iptal et
        message.value = ''; // Mesajı temizle
      };
  
     
const updateEmployee = async () => {
  // Kullanıcıdan onay almak için SweetAlert kullan
  const result = await Swal.fire({
    title: 'Emin misiniz?',
    text: 'Bu çalışanı güncellemek istediğinizden emin misiniz?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Evet, güncelle!',
    cancelButtonText: 'Hayır, iptal et'
  });

  // Kullanıcı güncellemeyi iptal ederse çık
  if (!result.isConfirmed) return;

  loading.value = true;
  try {
    await axios.put(
      `http://localhost:8000/api/employees/${selectedEmployee.value.id}`,
      {
        first_name: selectedEmployee.value.first_name,
        last_name: selectedEmployee.value.last_name,
        email: selectedEmployee.value.user.email,
      },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );

    // Güncellenen çalışanı listede güncelle
    const index = employees.value.findIndex(
      (emp) => emp.id === selectedEmployee.value.id
    );
    if (index !== -1) {
      employees.value[index] = { ...selectedEmployee.value };
    }

    selectedEmployee.value = null; // Düzenleme modunu kapat

    // SweetAlert2 ile başarı mesajı gösterme
    await Swal.fire({
      title: 'Başarılı!',
      text: 'Çalışan başarıyla güncellendi.',
      icon: 'success',
      confirmButtonText: 'Tamam',
    });
  } catch (error) {
    if (error.response.data.errors.email) {
        // E-posta adresi zaten kayıtlı ise
        Swal.fire({
          title: 'Hata!',
          text: 'Bu e-posta adresi zaten kayıtlı.',
          icon: 'error',
          confirmButtonText: 'Tamam'
        });
      }
    // SweetAlert2 ile hata mesajı gösterme
   else { await Swal.fire({
      title: 'Hata!',
      text: 'Çalışan güncellenemedi. Lütfen tekrar deneyin.',
      icon: 'error',
      confirmButtonText: 'Tamam',
    });}
  } finally {
    loading.value = false;
  }
};
  
      onMounted(fetchEmployees);
  
      return { employees, selectedEmployee, message, messageType, loading, deleteEmployee, editEmployee, cancelEdit, updateEmployee };
    },
  };
  </script>
  
  <style scoped>
  .employee-container{
    display: flex;
  justify-content: center;
  align-items: center;
  }
  .employee-list-container {
    width: 60%;
    margin-left: 250px;
    position: relative;
    top: 20% !important;
    transform: translateY(20%);
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow-x: auto;

  }
  
  .employee-list {
    list-style-type: none;
    padding: 0;
    max-width: 100%;
    min-width: 300px;
  }
  
  .employee-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
  }
  
  .edit-button, .delete-button {
    margin-left: 10px;
    padding: 5px 10px;
    font-size: 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .edit-button {
    background-color: #ffc107;
    color: white;
  }
  
  .delete-button {
    background-color: #f44336;
    color: white;
  }
  
  .edit-button:hover {
    background-color: #e0a800;
  }
  
  .delete-button:hover {
    background-color: #d32f2f;
  }
  
  .edit-form-container {
    margin-top: 20px;
    width: 100%;
    position: relative;
    top: 20% !important;
    transform: translateY(20%);
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
  
  .submit-button, .cancel-button {
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
  }
  
  .submit-button {
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
  }
  
  .cancel-button {
    background-color: #9e9e9e;
    color: white;
    border: none;
    border-radius: 4px;
    margin-top: 10px;
  }
  
  .submit-button:hover {
    background-color: #45a049;
  }
  
  .cancel-button:hover {
    background-color: #757575;
  }
  
  .alert.success {
    color: green;
    margin-top: 20px;
  }
  
  .alert.error {
    color: red;
    margin-top: 20px;
  }
  
  .loading {
    font-weight: bold;
    color: blue;
    margin-top: 20px;
  }
  .employee-form button[disabled] {
  background-color: #ccc;
  cursor: not-allowed;
}
 @media screen and (max-width: 576px) {
.employee-list-container {
  margin-left: 100px;
    width: 70%;
}
 }
  </style>
  