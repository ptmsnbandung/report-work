# 🚀 Panduan Deployment Kubernetes (K8s) - PT MSN Work Report

Dokumentasi ini menjelaskan cara men-deploy dan mengonfigurasi aplikasi **Work Report** di atas cluster **Kubernetes** agar mampu menangani lonjakan *traffic/request* tinggi secara otomatis (**Auto-Scaling**) dengan ketersediaan tinggi (**High Availability & Zero-Downtime**).

---

## 📁 Struktur File Kubernetes

| File | Deskripsi |
| :--- | :--- |
| [`00-namespace.yaml`](./00-namespace.yaml) | Namespace `work-report` untuk mengisolasi resource aplikasi. |
| [`01-configmap.yaml`](./01-configmap.yaml) | Pengaturan environment non-sensitif (URL, Log, Database Host, dll). |
| [`02-secret.yaml`](./02-secret.yaml) | Kredensial sensitif (`APP_KEY`, password database, dsb). |
| [`03-redis.yaml`](./03-redis.yaml) | Redis cluster pod untuk distributed session, cache, dan background queue. |
| [`04-storage-pvc.yaml`](./04-storage-pvc.yaml) | Shared Storage (ReadWriteMany) untuk foto dokumentasi & attachment. |
| [`05-app-deployment.yaml`](./05-app-deployment.yaml) | Pod Web App Laravel (PHP-FPM + Nginx) dengan Health Probes & Rolling Updates. |
| [`06-queue-worker-deployment.yaml`](./06-queue-worker-deployment.yaml) | Worker khusus untuk memproses background jobs (WhatsApp, PDF/Excel export). |
| [`07-scheduler-cronjob.yaml`](./07-scheduler-cronjob.yaml) | CronJob per menit untuk kalkulasi SLA otomatis (`schedule:run`). |
| [`08-service.yaml`](./08-service.yaml) | ClusterIP Service untuk routing internal ke Pod Web App. |
| [`09-hpa.yaml`](./09-hpa.yaml) | **Horizontal Pod Autoscaler (HPA)**: Otomatis menambah pod dari 3 hingga 20 saat traffic/CPU naik. |
| [`10-ingress.yaml`](./10-ingress.yaml) | Ingress routing domain `work-report.ptmsn.co.id` + Otomatis SSL/HTTPS. |

---

## 🛠️ Langkah-Langkah Deployment

### 1. Build & Push Docker Image
Jalankan perintah berikut di root folder proyek:
```bash
# 1. Build Docker Image
docker build -t ptmsn/work-report:latest .

# 2. Push ke Docker Registry (Docker Hub / GitHub Packages / Private Registry)
docker push ptmsn/work-report:latest
```

### 2. Konfigurasi Secret
Sesuaikan nilai `APP_KEY` dan password database pada file [`02-secret.yaml`](./02-secret.yaml).

### 3. Deploy ke Cluster Kubernetes
Terapkan seluruh manifest dengan 1 perintah:
```bash
kubectl apply -f k8s/
```

### 4. Periksa Status Pod & Autoscaler
```bash
# Cek seluruh pod yang berjalan
kubectl get pods -n work-report

# Cek status Auto-Scaler (HPA)
kubectl get hpa -n work-report

# Cek log aplikasi
kubectl logs -n work-report -l app=work-report-app --tail=100 -f
```

---

## ⚡ Bagaimana Kubernetes Menangani Lonjakan Request?

1. **Horizontal Pod Autoscaler (HPA):**
   * Saat CPU pod melebihi **70%** atau Memory melebihi **80%**, Kubernetes otomatis menambah jumlah Pod (hingga 20 Pod).
   * Traffic didistribusikan merata ke seluruh Pod aktif via Ingress / ClusterIP Service.
2. **Dedicated Queue Workers:**
   * Operasi berat seperti generate PDF Berita Acara, ekspor Excel berukuran besar, dan pengiriman notifikasi WhatsApp tidak akan membebani response time web user karena ditangani oleh worker terpisah di latar belakang.
3. **Rolling Updates (Zero Downtime):**
   * Saat ada pembaruan kode baru, pod lama hanya dimatikan setelah pod baru lolos uji kesehatan (`readiness probe`).
