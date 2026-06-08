import axios from 'axios';

const nodeApi = axios.create({
  baseURL: 'http://localhost:3001/api',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
  },
});

export default nodeApi;
