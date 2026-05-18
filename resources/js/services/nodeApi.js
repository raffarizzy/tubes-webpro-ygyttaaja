import axios from 'axios';

const nodeApi = axios.create({
  baseURL: 'http://localhost:3000/api',
  headers: {
    'Accept': 'application/json',
  },
});

export default nodeApi;
