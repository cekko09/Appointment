import * as yup from 'yup';

export const validationSchema = yup.object({
  postcode: yup.string().required('Posta kodu gereklidir'),
  address: yup.string().required('Adres gereklidir'),
  appointment_date: yup.string().required('Randevu tarihi gereklidir'),
  client_name: yup.string().required('Müşteri adı gereklidir'),
  client_email: yup.string().email('Geçerli bir email adresi giriniz').required('Müşteri emaili gereklidir'),
  client_phone: yup.string().required('Müşteri telefonu gereklidir'),
  employee_id: yup.number().required('Emlakçı çalışanı seçilmelidir')
});