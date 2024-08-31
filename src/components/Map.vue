<template>
  <div>
    <button @click="selectLocation">Adresi Seç</button>
    <div id="map"></div>
  </div>
</template>

<script>
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
  mounted() {
    this.loadMapScript();
  },
  methods: {
    loadMapScript() {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAunuRDlZ1mHwkhG0a_9YoEIfyScIQC5jo&libraries=places`;
        script.async = true;
        script.defer = true;
        script.onload = this.initMap;
        document.head.appendChild(script);
      },
    selectLocation() {
      const location = {
        lat: this.marker.getPosition().lat(),
        lng: this.marker.getPosition().lng(),
      };
      this.$emit('locationSelected', location);
    },
    selectLocation() {
      if (this.marker) {
        this.$emit('addressSelected', {
          lat: this.marker.getPosition().lat(),
          lng: this.marker.getPosition().lng(),
        });
      }
    },
    initMap() {
      const mapOptions = {
        center: { lat: this.initialLat, lng: this.initialLng },
        zoom: 14,
      };
      this.map = new google.maps.Map(document.getElementById('map'), mapOptions);
      
      this.marker = new google.maps.Marker({
        position: mapOptions.center,
        map: this.map,
        draggable: true,
      });

      google.maps.event.addListener(this.marker, 'dragend', (event) => {
        this.$emit('addressSelected', {
          lat: event.latLng.lat(),
          lng: event.latLng.lng(),
        });
      });
    },
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
