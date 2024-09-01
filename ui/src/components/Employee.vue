<template>
    <div class="employee-list-container">
      <h2>Çalışanlar Listesi</h2>
      <ul class="employee-list">
        <li v-for="employee in employees" :key="employee.id" class="employee-item">
          {{ employee.first_name }} {{ employee.last_name }} - {{ employee.email }}
          <button @click="editEmployee(employee)" class="edit-button">Düzenle</button>
          <button @click="deleteEmployee(employee.id)" class="delete-button">Sil</button>
        </li>
      </ul>
  
      <!-- Düzenleme Formu -->
      <div v-if="selectedEmployee" class="edit-form-container">
        <h3>Çalışanı Düzenle</h3>
        <form @submit.prevent="updateEmployee" class="employee-form">
          <input v-model="selectedEmployee.first_name" type="text" placeholder="Adı" required />
          <input v-model="selectedEmployee.last_name" type="text" placeholder="Soyadı" required />
          <input v-model="selectedEmployee.email" type="email" placeholder="E-posta" required />
          <button type="submit" class="submit-button">Kaydet</button>
          <button @click="cancelEdit" type="button" class="cancel-button">İptal</button>
        </form>
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
  </template>
  
  <script>
  import axios from 'axios';
  import { ref, onMounted } from 'vue';
  
  export default {
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
          employees.value = response.data;
          message.value = ''; // Önceki mesajı temizle
        } catch (error) {
          message.value = 'Çalışanlar yüklenemedi.';
          messageType.value = 'error';
        } finally {
          loading.value = false;
        }
      };
  
      const deleteEmployee = async (id) => {
        if (!confirm('Bu çalışanı silmek istediğinize emin misiniz?')) return;
        loading.value = true;
        try {
          await axios.delete(`http://localhost:8000/api/employees/${id}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          });
          employees.value = employees.value.filter(employee => employee.id !== id);
          message.value = 'Çalışan başarıyla silindi.';
          messageType.value = 'success';
        } catch (error) {
          message.value = 'Çalışan silinemedi.';
          messageType.value = 'error';
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
        loading.value = true;
        try {
          await axios.put(`http://localhost:8000/api/employees/${selectedEmployee.value.id}`, {
            first_name: selectedEmployee.value.first_name,
            last_name: selectedEmployee.value.last_name,
            email: selectedEmployee.value.email,
          }, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          });
          // Güncellenen çalışanı listede güncelle
          const index = employees.value.findIndex(emp => emp.id === selectedEmployee.value.id);
          if (index !== -1) {
            employees.value[index] = { ...selectedEmployee.value };
          }
          selectedEmployee.value = null; // Düzenleme modunu kapat
          message.value = 'Çalışan başarıyla güncellendi.';
          messageType.value = 'success';
        } catch (error) {
          message.value = 'Çalışan güncellenemedi.';
          messageType.value = 'error';
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
  .employee-list-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  
  .employee-list {
    list-style-type: none;
    padding: 0;
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
  </style>
  