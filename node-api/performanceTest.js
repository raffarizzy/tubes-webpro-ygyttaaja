import http from 'k6/http';
import { check, sleep } from 'k6';
export const options = {
    stages: [
        { duration: '30s', target: 20 },
        { duration: '1m', target: 20 },
        { duration: '20s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<3000'], // target SRS: < 3 detik 
        http_req_failed: ['rate<0.05'],  // sukses >= 95% 
    },
};
export default function () {
    const baseUrl = __ENV.BASE_URL || 'http://localhost:3001';
    const keywords = ['rem', 'oli', 'mesin', 'ban', 'busi', 'filter', 'shock', 'rantai'];
    const randomKeyword = keywords[Math.floor(Math.random() * keywords.length)];

    const res = http.get(`${baseUrl}/api/products?search=${randomKeyword}`);
    check(res, { 'status 200': (r) => r.status === 200 });
    sleep(1);
}