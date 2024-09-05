<template>
  <div>
    <button @click="selectLocation">Adresi Seç</button>
    <div id="map"></div>
  </div>
</template>

<script>
import {toRaw} from 'vue';
export default {
  name: 'Map',
  props: {
    initialLat: {
      type: Number,
      default: 51.5074, // Varsayılan olarak Londra
    },
    initialLng: {
      type: Number,
      default: -0.1278,
    }
  },
  data() {
    return {
      map: null,
      directionsService: null,
      directionsRenderer: null,
      marker: null,
      markers: [], // Tüm işaretçileri saklayacağız
    };
  },
  mounted() {
    this.loadMapScript();
  },
  methods: {
    loadMapScript() {
      const script = document.createElement('script');
      script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAunuRDlZ1mHwkhG0a_9YoEIfyScIQC5jo&libraries=places";
      script.async = true;
      script.defer = true;
      script.onload = this.initMap;
      document.head.appendChild(script);
    },
    initMap() {
      const mapOptions = {
        center: { lat: this.initialLat, lng: this.initialLng },
        zoom: 14,
      };
      this.map = new google.maps.Map(document.getElementById('map'), mapOptions);

      this.directionsService = new google.maps.DirectionsService();
      this.directionsRenderer = new google.maps.DirectionsRenderer({
        map: this.map,
        suppressMarkers: true,  // Varsayılan işaretçiyi devre dışı bırak
      });
      this.directionsRenderer.setMap(this.map);

      this.map.addListener('click', (event) => {
        this.placeMarker(event.latLng);
      });
    },
    placeMarker(location) {
      // Önceki tüm işaretçileri kaldır
      this.markers.map((marker) => toRaw(marker).setMap(null))
      this.markers = [];

      // Yeni işaretçiyi oluştur ve haritaya ekle
      const newMarker = new google.maps.Marker({
        position: location,
        map: this.map,
        draggable: true,
      });

      this.markers.push(newMarker);

      // Haritayı merkezle
      this.map.setCenter(location);

      // Adres bilgilerini alın
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ location: location }, (results, status) => {
        if (status === 'OK' && results[0]) {
          this.$emit('addressSelected', {
            lat: location.lat(),
            lng: location.lng(),
            formatted_address: results[0].formatted_address,
          });
        } else {
          console.error('Adres bulunamadı:', status);
        }
      });

      // Yol güzergahını güncelle
      this.calculateRoute(location);
    },
    calculateRoute(destination) {
      const request = {
        origin: { lat: this.initialLat, lng: this.initialLng },
        destination: destination,
        travelMode: 'DRIVING',
      };

      this.directionsService.route(request, (result, status) => {
        if (status === 'OK') {
          this.directionsRenderer.setDirections(result);
        } else {
          console.error('Yol hesaplanamadı:', status);
        }
      });
    },
    setMarker(lat, lng) {
      // Dışarıdan adres seçildiğinde bu fonksiyonu çağırarak haritada marker yerleştirebilirsiniz
      this.markers.map((marker) => toRaw(marker).setMap(null))
      this.markers = [];

      const location = { lat, lng };

      // Yeni işaretçiyi oluştur ve haritaya ekle
      const newMarker = new google.maps.Marker({
        position: location,
        map: this.map,
        draggable: true,
      });

      this.markers.push(newMarker);
      this.map.setCenter(location); // Haritayı merkezle

      this.calculateRoute(location); // Yol güzergahını güncelle
    }
  },
};
</script>

<style scoped>
#map {
  width: 100%;
  height: 300px;
  border-radius: 8px;
  overflow: hidden;
  margin-top: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
</style>