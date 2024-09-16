# Base image olarak Node.js kullan
FROM node:lts-alpine

# Serve paketini kur
RUN npm install -g serve

# Çalışma dizinini ayarla
WORKDIR /app

# package.json ve package-lock.json dosyalarını kopyala
COPY package*.json ./

# Bağımlılıkları yükle
RUN npm install

# Uygulama dosyalarını kopyala
COPY . .

# Projeyi üretim için derle
RUN npm run build

# Port aç
EXPOSE 8080

# 'serve' komutunu kullanarak 'dist' klasörünü sun
CMD ["serve", "-s", "dist", "-l", "8080"]
