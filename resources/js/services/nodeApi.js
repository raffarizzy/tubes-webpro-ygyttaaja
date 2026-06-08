import axios from 'axios';

const hostname = window.location.hostname;
const nodeApi = axios.create({
  baseURL: `http://${hostname}:3001/api`,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
  },
});

export default nodeApi;
