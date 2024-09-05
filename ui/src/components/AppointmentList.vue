<template>
  <div class="appointment-list-container">
    <h2>Randevular Listesi</h2>

    <!-- Yükleniyor mesajı -->
    <div v-if="loading" class="loading-message">Yükleniyor...</div>

    <!-- Çalışan filtreleme seçeneği -->
    <div v-else>
      <div class="filter-container">
        <label for="employee-filter">Emlakçı Çalışanı:</label>
        <select v-model="selectedEmployeeFilter" id="employee-filter" @change="fetchAppointments">
          <option value="">Tüm Emlakçılar</option>
          <option v-for="employee in employees" :key="employee.id" :value="employee.id">
            {{ employee.first_name }} {{ employee.last_name }}
          </option>
        </select>
      </div>

      <!-- Tarih Aralığı Filtreleme Seçeneği -->
      <div class="date-filter-container">
        <label for="start-date">Başlangıç Tarihi:</label>
        <input type="date" id="start-date" v-model="startDate" @change="filterAppointmentsByDate" />

        <label for="end-date">Bitiş Tarihi:</label>
        <input type="date" id="end-date" v-model="endDate" @change="filterAppointmentsByDate" />
      </div>

      <!-- Tarih Sıralama Select Box -->
      <div class="sort-container">
        <label for="sort-order">Tarihe Göre Sırala:</label>
        <select v-model="sortOrder" @change="sortAppointments">
          <option value="asc">En Eski</option>
          <option value="desc">En Yeni</option>
        </select>
      </div>
      
      <div style="overflow-x:auto;">
        <table class="appointment-table">
          <thead>
            <tr>
              <th>Müşteri Adı</th>
              <th>Randevu Tarihi</th>
              <th>Posta Kodu</th>
              <th>Emlakçı</th>
              <th>Ofisten Çıkış Zamanı</th>
              <th>Randevudan Sonra Müsait Olacağı Zaman</th>
              <th>İşlemler</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="appointment in sortedAppointments" :key="appointment.id" :class="{ past: isPastAppointment(appointment) }">
              <td>{{ appointment.client_name }}</td>
              <td>{{ formatDateTime(appointment.appointment_date) }}</td>
              <td>{{ appointment.postcode }}</td>
              <td>{{ getEmployeeById(appointment.employee_id).first_name }} {{ getEmployeeById(appointment.employee_id).last_name }}</td>
              <td>{{ appointment.departure_time }}</td>
              <td>{{ appointment.available_time }}</td>
              <td>
                <button @click="navigateToEdit(appointment.id)" class="edit-button">Düzenle</button>
                <button @click="deleteAppointment(appointment.id)" class="delete-button">Sil</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      appointments: [],
      employees: [],
      selectedEmployeeFilter: '',
      startDate: '',
      endDate: '',
      selectedAppointment: null,
      sortOrder: 'asc',  // Tarih sıralaması için varsayılan sıralama
      loading: false // Veri yüklenirken gösterilecek durumu
    };
  },
  computed: {
    // Tarih aralığına göre randevuları filtreler ve sıralar
    sortedAppointments() {
      let filteredAppointments = this.appointments.filter(appointment => {
        const appointmentDate = new Date(appointment.appointment_date);
        const start = this.startDate ? new Date(this.startDate) : null;
        const end = this.endDate ? new Date(this.endDate) : null;

        return (
          (!start || appointmentDate >= start) &&
          (!end || appointmentDate <= end)
        );
      });

      // Tarihe göre sıralama
      filteredAppointments.sort((a, b) => {
        if (this.sortOrder === 'asc') {
          return new Date(a.appointment_date) - new Date(b.appointment_date);  // En eski tarih önce
        } else {
          return new Date(b.appointment_date) - new Date(a.appointment_date);  // En yeni tarih önce
        }
      });

      return filteredAppointments;
    },
  },
  methods: {
    sortAppointments() {
      // Sıralama düzenini değiştirir
      this.sortedAppointments; // Sıralanmış randevuları yeniden hesapla
    },
    navigateToEdit(appointmentId) {
      this.$router.push({ name: 'AppointmentEdit', params: { id: appointmentId } });
    },
    async fetchEmployees() {
      this.loading = true; // Veri yükleniyor
      try {
        const response = await axios.get('http://localhost:8000/api/employees', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.employees = response.data;
      } catch (error) {
        console.error('Çalışanlar yüklenemedi:', error);
      } finally {
        this.loading = false; // Veri yüklenme tamamlandı
      }
    },
    async fetchAppointments() {
      this.loading = true; // Veri yükleniyor
      try {
        const response = await axios.get('http://localhost:8000/api/appointments', {
          params: { employee_id: this.selectedEmployeeFilter },  // Çalışana göre filtreleme
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.appointments = response.data;
      } catch (error) {
        console.error('Randevular yüklenemedi:', error);
      } finally {
        this.loading = false; // Veri yüklenme tamamlandı
      }
    },
    isPastAppointment(appointment) {
      return new Date(appointment.appointment_date) < new Date();  // Geçmiş randevuları kontrol eder
    },
    getEmployeeById(id) {
      return this.employees.find(employee => employee.id === id) || {};
    },
    formatDateTime(dateTime) {
      const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
      return new Date(dateTime).toLocaleDateString('tr-TR', options);
    },
    async deleteAppointment(appointmentId) {
      try {
        await axios.delete(`http://localhost:8000/api/appointments/${appointmentId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.appointments = this.appointments.filter(appt => appt.id !== appointmentId);
        this.$swal.fire({
          title: 'Randevu Silindi!',
          text: 'Randevu Başarıyla Silindi.',
          icon: 'success',
          confirmButtonText: 'Tamam'
        });
      } catch (error) {
        console.error('Randevu silinemedi:', error);
        this.$swal.fire({
          title: 'Hata!',
          text: 'Randevu Silinirken Hata Oluştu! Lütfen Tekrar Deneyin.',
          icon: 'error',
          confirmButtonText: 'Tamam'
        });
      }
    },
  },
  mounted() {
    this.fetchEmployees();  // Çalışanları getir
    this.fetchAppointments();  // Randevuları getir
  },
};
</script>


<style scoped>
/* Genel stiller */
.appointment-list-container {
  width: 80%;
  right: 10px;
  padding: 20px;
  position: absolute;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin-top: 40px;
}
.loading-message {
  text-align: center;
  font-size: 18px;
  font-weight: bold;
  color: blue;
  margin: 20px 0;
}

/* Filtreleme ve tarih aralığı konteynerleri */
.filter-container,
.date-filter-container {
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  flex-wrap: wrap; /* Flex öğelerinin taşmasını önlemek için */
}

.filter-container label,
.date-filter-container label {
  margin-right: 10px;
  font-weight: bold;
}

.filter-container select,
.date-filter-container input {
  padding: 8px;
  font-size: 16px;
  border-radius: 4px;
  border: 1px solid #ccc;
  margin-right: 15px;
  flex: 1; /* Esnek boyutlandırma */
}

/* Tablo stilleri */
.appointment-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.appointment-table th,
.appointment-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.appointment-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.appointment-table tr:hover {
  background-color: #f1f1f1;
}

.appointment-table .past {
  color: gray;
  text-decoration: line-through;
}

/* Buton stilleri */
.edit-button,
.delete-button {
  margin-left: 5px;
  padding: 8px 12px;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s ease;margin-bottom: 5px;
}

.edit-button {
  background-color: #ffc107;
  color: white;
}

.edit-button:hover {
  background-color: #e0a800;
}

.delete-button {
  background-color: #e74c3c;
  color: white;
}

.delete-button:hover {
  background-color: #c0392b;
}

/* Medya sorguları */

/* 768px ve altı */


</style>
