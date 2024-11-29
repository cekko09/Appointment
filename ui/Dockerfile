FROM node:lts as development
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
EXPOSE 8080

# 'serve' komutunu kullanarak 'dist' klasörünü sun
CMD ["npm", "run", "dev"]
