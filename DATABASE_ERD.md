# Struktur Database Cakapku (ERD)

Di bawah ini merupakan ilustrasi keterkaitan (*Entity-Relationship Diagram*) seluruh tabel di dalam _Database_ Cakapku beserta integrasi _role_ menggunakan `spatie/laravel-permission`:

```mermaid
erDiagram
    SATKERS ||--o{ USERS : "has many (pegawai)"
    SATKERS {
        bigint id PK
        bigint id_pimpinan FK "Nullable (To Users.id)"
        string nama_satker
        timestamps created_at
    }

    USERS ||--|{ KINERJA_HARIANS : "creates"
    USERS }o--o{ ROLES : "has roles (via model_has_roles)"
    USERS {
        bigint id PK
        bigint id_satker FK "Nullable"
        string username "Unique"
        string nip "Nullable"
        string nama
        string email
        string jabatan "Nullable"
        string gol_ruang "Nullable"
        string password
        timestamps created_at
    }

    PERIODES ||--o{ PERKINS : "contains"
    PERIODES {
        bigint id PK
        string tahun
        boolean status
        timestamps created_at
    }

    PERKINS ||--o{ IKSKS : "has Indikators"
    PERKINS }o--o{ SATKERS : "assigned to (via perkin_satker)"
    PERKINS {
        bigint id PK
        string nama_perkin
        string no_sk "Nullable"
        bigint id_periode FK
        boolean status
        bigint created_by FK "To Users.id"
        timestamps created_at
    }

    PERKIN_SATKER {
        bigint id_perkin FK
        bigint id_satker FK
    }

    IKSKS ||--o{ KINERJA_HARIANS : "reported in"
    IKSKS {
        bigint id PK
        bigint id_perkin FK
        text indikator
        string target_vol "Nullable"
        string target_satuan "Nullable"
        timestamps created_at
    }

    KINERJA_HARIANS {
        bigint id PK
        bigint id_user FK
        bigint id_iksk FK
        text uraian_pekerjaan
        date tgl_laporan
        enum status_kehadiran
        timestamps created_at
    }

    %% Spatie permission core simplified
    ROLES {
        bigint id PK
        string name
        string guard_name
    }
```

## Keterangan Singkat:
1. **Users dengan Roles**: Telah didukung oleh pustaka `spatie`, sehingga peran `ADMIN`, `PIMPINAN`, dll melekat secara terpisah via tabel pivot di balik layar.
2. **Perkin_Satker**: Tabel jembatan (*pivot table*) yang membuat satu dokumen Perkin (Perjanjian Kinerja) bisa didistribusikan ke banyak Unit Satker sekaligus.
3. **Users (Struktur Baru)**: Penambahan atribut Username opsional seperti `username` dan kolom `nip` digunakan untuk otentikasi login multi-kolom yang dinamis.
