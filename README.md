# 📚 Perpustakaan Digital (Digital Library)

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0+">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Tests-156%20Passed-4ade80?style=for-the-badge&logo=phpunit&logoColor=white" alt="Tests">
</p>

<p align="center">
  Sistem Perpustakaan Digital modern dengan fitur peminjaman buku, ulasan, rekomendasi cerdas berbasis algoritma hybrid, dan manajemen denda otomatis.
</p>

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Tech Stack](#-tech-stack)
- [Instalasi](#-instalasi)
- [ERD (Entity Relationship Diagram)](#-erd-entity-relationship-diagram)
- [Class Diagram](#-class-diagram)
- [Use Case Diagram](#-use-case-diagram)
- [Activity Diagrams](#-activity-diagrams)
- [Sequence Diagrams](#-sequence-diagrams)
- [State Diagram](#-state-diagram)
- [Struktur Database](#-struktur-database)
- [API Routes](#-api-routes)
- [Testing](#-testing)
- [Screenshots](#-screenshots)
- [Kontributor](#-kontributor)
- [Lisensi](#-lisensi)

---

## ✨ Fitur Utama

### 👤 Member (Anggota)
- 📖 Browse & search katalog buku dengan filter kategori
- 📝 Request peminjaman buku online
- ⭐ Tulis ulasan dan rating buku (1-5 bintang)
- 👍 Vote helpful/not helpful pada ulasan
- 🔄 Perpanjang peminjaman (max 5x)
- 💡 Rekomendasi buku personal berbasis AI (Hybrid Algorithm)
- 💰 Lihat dan bayar denda keterlambatan
- 🔔 Notifikasi real-time

### 👨‍💼 Librarian (Pustakawan)
- ✅ Approve/reject permintaan peminjaman
- 📦 Konfirmasi pengambilan buku
- 📥 Proses pengembalian buku
- 🔍 Moderasi ulasan buku
- 💵 Kelola pembayaran denda
- 📊 Dashboard statistik peminjaman

### 👑 Admin
- 📚 CRUD manajemen buku
- 📁 CRUD manajemen kategori (hierarchical)
- 👥 CRUD manajemen user
- 🚫 Suspend/activate user
- 📈 Dashboard analytics lengkap

---

## 🏗 Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              Blade Templates + Tailwind CSS           │   │
│  │                  (Responsive Design)                  │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                        │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              Laravel 12 MVC Framework                 │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │   Auth   │  │  Catalog │  │   Loan   │  │  Review  │   │
│  │  Module  │  │  Module  │  │  Module  │  │  Module  │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
│                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                  │
│  │  Notif   │  │  Recom   │  │   Fine   │                  │
│  │  Module  │  │  Engine  │  │  Module  │                  │
│  └──────────┘  └──────────┘  └──────────┘                  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                       DATA LAYER                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                    MySQL 8.0+                         │   │
│  │              Eloquent ORM + Migrations                │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🛠 Tech Stack

| Category | Technology |
|----------|------------|
| **Backend Framework** | Laravel 12.x |
| **Frontend** | Blade Templates, Tailwind CSS 3.x, Alpine.js |
| **Database** | MySQL 8.0+ |
| **Authentication** | Laravel Breeze |
| **Authorization** | Spatie Laravel Permission 6.x |
| **Build Tools** | Vite, PostCSS |
| **Testing** | PHPUnit 11.x |
| **PHP Version** | 8.2+ |

---

## 🚀 Instalasi

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer 2.x
- MySQL 8.0+
- Node.js 18+ & npm

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/digital-library.git
cd digital-library

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=perpusrpldb
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Install Node dependencies
npm install

# 8. Build assets
npm run build

# 9. Jalankan development server
php artisan serve
```

### Quick Start dengan Composer Script

```bash
# Setup lengkap (install, migrate, build)
composer setup

# Development mode (server, queue, logs, vite)
composer dev

# Jalankan tests
composer test
```

---

## 📊 ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    USERS ||--o{ LOANS : "meminjam"
    USERS ||--o{ REVIEWS : "menulis"
    USERS ||--o{ REVIEW_VOTES : "memberikan_vote"
    USERS ||--o{ NOTIFICATIONS : "menerima"
    USERS ||--o{ FINES : "memiliki"
    USERS ||--o{ RECOMMENDATIONS : "menerima"
    
    BOOKS ||--o{ LOANS : "dipinjam"
    BOOKS ||--o{ REVIEWS : "direview"
    BOOKS }o--|| CATEGORIES : "termasuk"
    BOOKS ||--o{ RECOMMENDATIONS : "direkomendasikan"
    
    LOANS ||--o{ FINES : "menghasilkan"
    LOANS ||--o{ NOTIFICATIONS : "memicu"
    
    REVIEWS ||--o{ REVIEW_VOTES : "mendapat_vote"
    
    CATEGORIES ||--o{ CATEGORIES : "parent_child"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string phone
        text address
        string status
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }
    
    BOOKS {
        bigint id PK
        string isbn UK
        string title
        string author
        string publisher
        int publication_year
        bigint category_id FK
        int total_copies
        int available_copies
        text description
        string cover_image
        string language
        int pages
        timestamp created_at
        timestamp updated_at
    }
    
    CATEGORIES {
        bigint id PK
        string name UK
        text description
        bigint parent_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    LOANS {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        datetime request_date
        datetime approval_date
        datetime pickup_date
        date due_date
        datetime return_date
        string status
        bigint approved_by FK
        int renewal_count
        text notes
        timestamp created_at
        timestamp updated_at
    }
    
    REVIEWS {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        int rating
        text review_text
        int helpful_count
        int not_helpful_count
        string status
        bigint moderated_by FK
        datetime moderated_at
        timestamp created_at
        timestamp updated_at
    }
    
    REVIEW_VOTES {
        bigint id PK
        bigint review_id FK
        bigint user_id FK
        string vote_type
        timestamp created_at
        timestamp updated_at
    }
    
    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string type
        text message
        boolean is_read
        bigint loan_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    FINES {
        bigint id PK
        bigint loan_id FK
        bigint user_id FK
        decimal amount
        text reason
        string status
        datetime paid_date
        timestamp created_at
        timestamp updated_at
    }
    
    RECOMMENDATIONS {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        string type
        decimal score
        datetime generated_at
        boolean clicked
        timestamp created_at
        timestamp updated_at
    }
```

---

## 📐 Class Diagram

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password
        +string phone
        +string address
        +string status
        +loans() HasMany~Loan~
        +reviews() HasMany~Review~
        +fines() HasMany~Fine~
        +notifications() HasMany~Notification~
        +recommendations() HasMany~Recommendation~
        +activeLoans() HasMany~Loan~
        +canBorrowBooks() bool
        +hasUnpaidFines() bool
        +isAdmin() bool
        +isLibrarian() bool
        +isMember() bool
    }

    class Book {
        +int id
        +string isbn
        +string title
        +string author
        +string publisher
        +int publication_year
        +int category_id
        +int total_copies
        +int available_copies
        +string description
        +string cover_image
        +category() BelongsTo~Category~
        +loans() HasMany~Loan~
        +reviews() HasMany~Review~
        +approvedReviews() HasMany~Review~
        +recommendations() HasMany~Recommendation~
        +getAverageRatingAttribute() float
        +isAvailable() bool
        +decrementAvailableCopies() void
        +incrementAvailableCopies() void
    }

    class Category {
        +int id
        +string name
        +string description
        +int parent_id
        +parent() BelongsTo~Category~
        +children() HasMany~Category~
        +books() HasMany~Book~
        +allBooks() Collection~Book~
    }

    class Loan {
        +int id
        +int user_id
        +int book_id
        +datetime request_date
        +datetime approval_date
        +datetime pickup_date
        +date due_date
        +datetime return_date
        +string status
        +int renewal_count
        +user() BelongsTo~User~
        +book() BelongsTo~Book~
        +approver() BelongsTo~User~
        +fine() HasOne~Fine~
        +notifications() HasMany~Notification~
        +isOverdue() bool
        +canBeRenewed() bool
        +daysOverdue() int
        +approve(User approver) void
        +reject() void
        +pickup() void
        +returnBook() void
        +cancel() void
    }

    class Review {
        +int id
        +int user_id
        +int book_id
        +int rating
        +string review_text
        +int helpful_count
        +int not_helpful_count
        +string status
        +user() BelongsTo~User~
        +book() BelongsTo~Book~
        +votes() HasMany~ReviewVote~
        +hasVotedBy(int userId) bool
        +getVoteByUser(int userId) string
        +approve() void
        +reject() void
    }

    class ReviewVote {
        +int id
        +int review_id
        +int user_id
        +string vote_type
        +review() BelongsTo~Review~
        +user() BelongsTo~User~
    }

    class Fine {
        +int id
        +int loan_id
        +int user_id
        +decimal amount
        +string reason
        +string status
        +datetime paid_date
        +loan() BelongsTo~Loan~
        +user() BelongsTo~User~
        +isPaid() bool
        +pay() void
        +waive() void
    }

    class Notification {
        +int id
        +int user_id
        +string type
        +string message
        +bool is_read
        +int loan_id
        +user() BelongsTo~User~
        +loan() BelongsTo~Loan~
        +markAsRead() void
    }

    class Recommendation {
        +int id
        +int user_id
        +int book_id
        +string type
        +float score
        +datetime generated_at
        +bool clicked
        +user() BelongsTo~User~
        +book() BelongsTo~Book~
    }

    class RecommendationService {
        -float WEIGHT_CONTENT_BASED
        -float WEIGHT_COLLABORATIVE
        -float WEIGHT_POPULARITY
        -float WEIGHT_RECENCY
        +generateRecommendations(User user, int limit) Collection~Book~
        +getCachedRecommendations(User user, int limit) Collection~Book~
        +getTrendingBooks(int limit) Collection~Book~
        +getTopRatedBooks(int limit) Collection~Book~
        +getNewArrivals(int limit) Collection~Book~
        -calculateHybridScore(Book book) float
        -calculateContentBasedScore(Book book) float
        -calculateCollaborativeScore(Book book) float
        -calculatePopularityScore(Book book) float
        -calculateRecencyScore(Book book) float
    }

    User "1" -- "*" Loan : has
    User "1" -- "*" Review : writes
    User "1" -- "*" Fine : owes
    User "1" -- "*" Notification : receives
    User "1" -- "*" Recommendation : receives
    
    Book "1" -- "*" Loan : borrowed_in
    Book "1" -- "*" Review : has
    Book "*" -- "1" Category : belongs_to
    Book "1" -- "*" Recommendation : recommended_in
    
    Loan "1" -- "0..1" Fine : generates
    Loan "1" -- "*" Notification : triggers
    
    Review "1" -- "*" ReviewVote : has
    
    Category "1" -- "*" Category : has_children
    
    RecommendationService ..> User : uses
    RecommendationService ..> Book : uses
    RecommendationService ..> Recommendation : creates
```

---

## 🎭 Use Case Diagram

```mermaid
graph TB
    subgraph "Digital Library System"
        subgraph "Public Access"
            UC1[Browse Katalog Buku]
            UC2[Search Buku]
            UC3[Lihat Detail Buku]
            UC4[Lihat Review Buku]
        end

        subgraph "Member Features"
            UC5[Login/Register]
            UC6[Request Peminjaman]
            UC7[Lihat Status Peminjaman]
            UC8[Perpanjang Peminjaman]
            UC9[Cancel Request]
            UC10[Tulis Review]
            UC11[Vote Review]
            UC12[Lihat Rekomendasi]
            UC13[Lihat Denda]
            UC14[Bayar Denda]
            UC15[Edit Profile]
            UC16[Lihat Notifikasi]
        end

        subgraph "Librarian Features"
            UC17[Approve Peminjaman]
            UC18[Reject Peminjaman]
            UC19[Konfirmasi Pickup]
            UC20[Proses Pengembalian]
            UC21[Moderasi Review]
            UC22[Kelola Denda]
            UC23[Lihat Dashboard Librarian]
        end

        subgraph "Admin Features"
            UC24[CRUD Buku]
            UC25[CRUD Kategori]
            UC26[CRUD User]
            UC27[Suspend User]
            UC28[Activate User]
            UC29[Lihat Dashboard Admin]
        end
    end

    Guest((Guest))
    Member((Member))
    Librarian((Librarian))
    Admin((Admin))

    Guest --> UC1
    Guest --> UC2
    Guest --> UC3
    Guest --> UC4
    Guest --> UC5

    Member --> UC1
    Member --> UC2
    Member --> UC3
    Member --> UC4
    Member --> UC6
    Member --> UC7
    Member --> UC8
    Member --> UC9
    Member --> UC10
    Member --> UC11
    Member --> UC12
    Member --> UC13
    Member --> UC14
    Member --> UC15
    Member --> UC16

    Librarian --> UC1
    Librarian --> UC2
    Librarian --> UC3
    Librarian --> UC17
    Librarian --> UC18
    Librarian --> UC19
    Librarian --> UC20
    Librarian --> UC21
    Librarian --> UC22
    Librarian --> UC23

    Admin --> UC24
    Admin --> UC25
    Admin --> UC26
    Admin --> UC27
    Admin --> UC28
    Admin --> UC29
    Admin --> UC17
    Admin --> UC18
    Admin --> UC19
    Admin --> UC20
    Admin --> UC21
    Admin --> UC22
```

---

## 🔄 Activity Diagrams

### Flowchart Proses Peminjaman

```mermaid
flowchart TD
    Start([User Login]) --> Browse[Browse Katalog Buku]
    Browse --> Search{Cari Buku<br/>Tertentu?}
    Search -->|Ya| SearchBox[Gunakan Search & Filter]
    Search -->|Tidak| BrowseAll[Lihat Semua Buku]
    SearchBox --> ViewBook[Lihat Detail Buku]
    BrowseAll --> ViewBook
    
    ViewBook --> CheckAvail{Buku<br/>Tersedia?}
    CheckAvail -->|Tidak| Notif1[Tampilkan: Buku Sedang Dipinjam]
    Notif1 --> End1([Selesai])
    
    CheckAvail -->|Ya| CheckQuota{User Sudah<br/>Pinjam 3 Buku?}
    CheckQuota -->|Ya| Notif2[Tampilkan: Quota Penuh]
    Notif2 --> End2([Selesai])
    
    CheckQuota -->|Tidak| CheckFine{User Punya<br/>Denda Belum Lunas?}
    CheckFine -->|Ya| Notif3[Tampilkan: Lunasi Denda Dulu]
    Notif3 --> End3([Selesai])
    
    CheckFine -->|Tidak| RequestLoan[Klik Request Buku]
    RequestLoan --> SaveRequest[Simpan Request ke Database<br/>Status: Requested]
    SaveRequest --> NotifUser[Kirim Notifikasi ke User:<br/>Request Berhasil]
    NotifUser --> NotifLibrarian[Kirim Notifikasi ke Pustakawan:<br/>Ada Request Baru]
    
    NotifLibrarian --> LibrarianReview[Pustakawan Review Request]
    LibrarianReview --> LibDecision{Pustakawan<br/>Approve?}
    
    LibDecision -->|Reject| UpdateReject[Update Status: Rejected]
    UpdateReject --> NotifReject[Kirim Notifikasi Reject ke User]
    NotifReject --> End4([Selesai])
    
    LibDecision -->|Approve| UpdateApprove[Update Status: Approved<br/>Set Due Date: +7 hari]
    UpdateApprove --> DecreaseCopy[Kurangi Available Copies]
    DecreaseCopy --> NotifApprove[Kirim Notifikasi Approve ke User]
    NotifApprove --> UserPickup[User Datang Ambil Buku]
    UserPickup --> UpdateActive[Update Status: Active<br/>Set Pickup Date]
    UpdateActive --> End5([Buku Dipinjam])
```

### Flowchart Proses Pengembalian

```mermaid
flowchart TD
    Start([User Kembalikan Buku]) --> ScanBook[Pustakawan Cek Data Peminjaman]
    ScanBook --> FindLoan[Cari Data Peminjaman di Database]
    FindLoan --> CheckLoan{Data<br/>Ditemukan?}
    
    CheckLoan -->|Tidak| Error1[Tampilkan Error:<br/>Data Tidak Ditemukan]
    Error1 --> End1([Selesai])
    
    CheckLoan -->|Ya| CheckStatus{Status<br/>Peminjaman?}
    CheckStatus -->|Bukan Active| Error2[Tampilkan Error:<br/>Status Tidak Valid]
    Error2 --> End2([Selesai])
    
    CheckStatus -->|Active| GetDates[Ambil Due Date & Return Date]
    GetDates --> CalcDays[Hitung Selisih Hari:<br/>Days = Return Date - Due Date]
    CalcDays --> CheckOverdue{Days > 0?<br/>Terlambat?}
    
    CheckOverdue -->|Tidak| NoFine[Tidak Ada Denda]
    NoFine --> UpdateReturn[Update Loan:<br/>Status = Returned<br/>Return Date = Today]
    
    CheckOverdue -->|Ya| CheckGrace{Days <= 3?<br/>Grace Period?}
    CheckGrace -->|Ya| NoFine
    
    CheckGrace -->|Tidak| CalcFine[Hitung Denda:<br/>Fine = Days × Rp 1000]
    CalcFine --> CheckMax{Fine ><br/>Rp 50000?}
    CheckMax -->|Ya| SetMax[Set Fine = Rp 50000]
    CheckMax -->|Tidak| UseFine[Gunakan Fine yang Dihitung]
    
    SetMax --> CreateFine[Buat Record di Tabel FINES<br/>Status: Unpaid]
    UseFine --> CreateFine
    CreateFine --> NotifFine[Kirim Notifikasi Denda ke User]
    NotifFine --> UpdateReturnFine[Update Loan:<br/>Status = Returned<br/>Return Date = Today]
    
    UpdateReturn --> IncreaseCopy[Tambah Available Copies]
    UpdateReturnFine --> IncreaseCopy
    IncreaseCopy --> NotifReturn[Kirim Notifikasi:<br/>Pengembalian Berhasil]
    NotifReturn --> End3([Selesai])
```

### Flowchart Sistem Rekomendasi (Hybrid Algorithm)

```mermaid
flowchart TD
    Start([User Login ke Dashboard]) --> CheckHistory{User Punya<br/>History?}
    
    CheckHistory -->|Tidak| NewUser[User Baru]
    NewUser --> PopRec[Rekomendasi Popularity-Based<br/>Weight: 60%]
    PopRec --> RecencyRec[Rekomendasi Recency-Based<br/>Weight: 40%]
    RecencyRec --> CombineNew[Combine & Rank]
    CombineNew --> ShowRec[Tampilkan Top 10 Rekomendasi]
    ShowRec --> End1([Selesai])
    
    CheckHistory -->|Ya| ExistUser[User dengan History]
    ExistUser --> GetLoans[Ambil Data Loans User]
    GetLoans --> GetReviews[Ambil Data Reviews User]
    GetReviews --> ContentBased[Content-Based Filtering<br/>Analisis Category & Author<br/>Weight: 40%]
    
    ContentBased --> GetSimilarUsers[Cari User dengan<br/>Preferensi Serupa]
    GetSimilarUsers --> CollabFilter[Collaborative Filtering<br/>Analisis Rating Patterns<br/>Weight: 30%]
    
    CollabFilter --> PopularBooks[Ambil Buku Populer<br/>Popularity-Based<br/>Weight: 20%]
    PopularBooks --> RecentBooks[Ambil Buku Terbaru<br/>Recency-Based<br/>Weight: 10%]
    
    RecentBooks --> CalcScore[Hitung Total Score:<br/>Score = CB×0.4 + CF×0.3 + Pop×0.2 + Rec×0.1]
    CalcScore --> FilterRead{Buku Sudah<br/>Dipinjam User?}
    FilterRead -->|Ya| Exclude[Exclude dari List]
    FilterRead -->|Tidak| Include[Include dalam List]
    
    Exclude --> CheckMore{Masih Ada<br/>Buku Lain?}
    Include --> CheckMore
    CheckMore -->|Ya| FilterRead
    CheckMore -->|Tidak| SortScore[Sort by Score Descending]
    
    SortScore --> Top10[Ambil Top 10]
    Top10 --> SaveRec[Simpan ke Tabel RECOMMENDATIONS]
    SaveRec --> ShowRecExist[Tampilkan Rekomendasi di Dashboard]
    ShowRecExist --> End2([Selesai])
```

### Flowchart Proses Review

```mermaid
flowchart LR
    A([Start: User Login]) --> B[Klik Menu My Loans]
    B --> C[Lihat Daftar Buku<br/>yang Pernah Dipinjam]
    C --> D{Pilih Buku<br/>untuk Review}
    D --> E[Klik Tombol Write Review]
    E --> F{Sudah Pernah<br/>Review Buku Ini?}
    F -->|Ya| G[Tampilkan Error:<br/>Sudah Pernah Review]
    G --> H([End])
    F -->|Tidak| I[Tampilkan Form Review]
    I --> J[User Pilih Rating:<br/>1-5 Stars]
    J --> K[User Tulis Review Text<br/>Min 10 karakter]
    K --> L[Klik Submit Review]
    L --> M{Validasi<br/>Form?}
    M -->|Gagal| N[Tampilkan Error:<br/>Isi Semua Field]
    N --> I
    M -->|Sukses| O[Simpan Review ke Database<br/>Status: Pending]
    O --> P[Tampilkan Notifikasi:<br/>Review Berhasil Dikirim]
    P --> Q[Pustakawan<br/>Moderasi Review]
    Q --> R{Review<br/>Approved?}
    R -->|Reject| S[Status: Rejected]
    S --> H
    R -->|Approve| T[Status: Approved]
    T --> U[Review Muncul di Book Detail]
    U --> V[User Lain Bisa Vote:<br/>Helpful / Not Helpful]
    V --> W([End: Review Published])
```

---

## 📊 Sequence Diagrams

### Sequence Diagram: Proses Peminjaman Buku

```mermaid
sequenceDiagram
    actor Member
    participant BookController
    participant LoanController
    participant Book as Book Model
    participant Loan as Loan Model
    participant User as User Model
    participant NotificationService
    actor Librarian

    Member->>BookController: GET /books/{id}
    BookController->>Book: findOrFail(id)
    Book-->>BookController: book data
    BookController-->>Member: Show book detail page

    Member->>LoanController: POST /books/{id}/borrow
    LoanController->>User: Check canBorrowBooks()
    alt Cannot borrow (quota/fines)
        User-->>LoanController: false
        LoanController-->>Member: Error: Cannot borrow
    else Can borrow
        User-->>LoanController: true
        LoanController->>Book: Check isAvailable()
        alt Book not available
            Book-->>LoanController: false
            LoanController-->>Member: Error: Book not available
        else Book available
            Book-->>LoanController: true
            LoanController->>Loan: create(user_id, book_id, status='requested')
            Loan-->>LoanController: loan created
            LoanController->>NotificationService: notifyMember(loan_requested)
            LoanController->>NotificationService: notifyLibrarian(new_request)
            LoanController-->>Member: Success: Request submitted
        end
    end

    Note over Librarian: Reviews pending requests
    Librarian->>LoanController: POST /librarian/loans/{id}/approve
    LoanController->>Loan: update(status='approved', due_date)
    LoanController->>Book: decrementAvailableCopies()
    LoanController->>NotificationService: notifyMember(loan_approved)
    LoanController-->>Librarian: Success: Loan approved

    Member->>LoanController: Visit library for pickup
    Librarian->>LoanController: POST /librarian/loans/{id}/pickup
    LoanController->>Loan: update(status='active', pickup_date)
    LoanController-->>Librarian: Success: Pickup confirmed
```

### Sequence Diagram: Menulis Review

```mermaid
sequenceDiagram
    actor Member
    participant ReviewController
    participant Review as Review Model
    participant Book as Book Model
    participant Loan as Loan Model
    actor Librarian

    Member->>ReviewController: POST /books/{id}/reviews
    ReviewController->>Loan: Check user has borrowed book
    alt Never borrowed
        Loan-->>ReviewController: no record
        ReviewController-->>Member: Error: Must borrow first
    else Has borrowed
        Loan-->>ReviewController: loan exists
        ReviewController->>Review: Check existing review
        alt Already reviewed
            Review-->>ReviewController: review exists
            ReviewController-->>Member: Error: Already reviewed
        else No review yet
            ReviewController->>Review: create(rating, review_text, status='pending')
            Review-->>ReviewController: review created
            ReviewController-->>Member: Success: Review submitted for moderation
        end
    end

    Note over Librarian: Moderates pending reviews
    Librarian->>ReviewController: POST /librarian/reviews/{id}/approve
    ReviewController->>Review: update(status='approved')
    Review-->>ReviewController: updated
    ReviewController-->>Librarian: Success: Review approved
```

### Sequence Diagram: Sistem Rekomendasi

```mermaid
sequenceDiagram
    actor Member
    participant DashboardController
    participant RecommendationService
    participant Loan as Loan Model
    participant Review as Review Model
    participant Book as Book Model
    participant Recommendation as Recommendation Model

    Member->>DashboardController: GET /dashboard
    DashboardController->>RecommendationService: getCachedRecommendations(user, 10)
    
    alt Cache exists
        RecommendationService-->>DashboardController: cached recommendations
    else No cache
        RecommendationService->>Loan: Get user's borrowing history
        Loan-->>RecommendationService: borrowed book IDs
        RecommendationService->>Review: Get user's reviews & ratings
        Review-->>RecommendationService: user preferences
        
        RecommendationService->>Book: Get available books
        Book-->>RecommendationService: all available books
        
        Note over RecommendationService: Calculate scores:<br/>Content-Based (40%)<br/>Collaborative (30%)<br/>Popularity (20%)<br/>Recency (10%)
        
        RecommendationService->>Recommendation: Store recommendations
        Recommendation-->>RecommendationService: saved
        RecommendationService-->>DashboardController: top 10 books
    end
    
    DashboardController-->>Member: Dashboard with recommendations
```

---

## 🔄 State Diagram

### State Diagram: Status Peminjaman (Loan)

```mermaid
stateDiagram-v2
    [*] --> Requested: Member request buku
    
    Requested --> Approved: Librarian approve
    Requested --> Rejected: Librarian reject
    Requested --> Cancelled: Member cancel
    
    Approved --> Active: Member pickup buku
    Approved --> Cancelled: Request expired/cancelled
    
    Active --> Returned: Buku dikembalikan tepat waktu
    Active --> Overdue: Melewati due date
    
    Overdue --> Returned: Buku dikembalikan + denda
    
    Returned --> [*]
    Rejected --> [*]
    Cancelled --> [*]
    
    note right of Requested
        Status awal ketika member
        melakukan request peminjaman
    end note
    
    note right of Approved
        Buku di-reserve untuk member
        Available copies berkurang
    end note
    
    note right of Active
        Buku dalam peminjaman aktif
        Countdown menuju due date
    end note
    
    note right of Overdue
        Melewati due date + grace period
        Denda mulai dihitung
    end note
```

### State Diagram: Status Review

```mermaid
stateDiagram-v2
    [*] --> Pending: Member submit review
    
    Pending --> Approved: Librarian approve
    Pending --> Rejected: Librarian reject
    
    Approved --> [*]: Published & Visible
    Rejected --> [*]: Hidden from public
    
    note right of Pending
        Review menunggu moderasi
        Tidak tampil ke publik
    end note
    
    note right of Approved
        Review tampil di halaman buku
        Member lain bisa vote helpful/not helpful
    end note
```

### State Diagram: Status Denda (Fine)

```mermaid
stateDiagram-v2
    [*] --> Unpaid: Denda dibuat (keterlambatan)
    
    Unpaid --> Paid: Member bayar denda
    Unpaid --> Waived: Librarian waive denda
    
    Paid --> [*]
    Waived --> [*]
    
    note right of Unpaid
        Member tidak bisa pinjam buku baru
        sampai denda dilunasi
    end note
```

---

## 🗄 Struktur Database

### Tabel dan Deskripsi

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (member, librarian, admin) |
| `books` | Data buku dalam perpustakaan |
| `categories` | Kategori buku (hierarchical) |
| `loans` | Transaksi peminjaman buku |
| `reviews` | Ulasan dan rating buku |
| `review_votes` | Vote helpful/not helpful pada review |
| `fines` | Denda keterlambatan pengembalian |
| `notifications` | Notifikasi untuk pengguna |
| `recommendations` | Rekomendasi buku personal |
| `roles` | Role pengguna (Spatie Permission) |
| `permissions` | Permission granular (Spatie Permission) |
| `model_has_roles` | Relasi user-role |

### Konstanta Sistem

| Konstanta | Nilai | Deskripsi |
|-----------|-------|-----------|
| `MAX_ACTIVE_LOANS` | 3 | Maksimal peminjaman aktif per member |
| `LOAN_DURATION` | 7 hari | Durasi peminjaman standar |
| `GRACE_PERIOD` | 3 hari | Periode toleransi tanpa denda |
| `MAX_RENEWALS` | 5 | Maksimal perpanjangan per peminjaman |
| `FINE_PER_DAY` | Rp 1.000 | Denda per hari keterlambatan |
| `MAX_FINE` | Rp 50.000 | Maksimal total denda per peminjaman |

### Bobot Algoritma Rekomendasi

| Komponen | Bobot | Deskripsi |
|----------|-------|-----------|
| Content-Based | 40% | Berdasarkan kategori & author yang disukai |
| Collaborative | 30% | Berdasarkan user dengan preferensi serupa |
| Popularity | 20% | Berdasarkan jumlah peminjaman |
| Recency | 10% | Berdasarkan buku terbaru |

---

## 🛣 API Routes

### Public Routes
| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/` | Closure | Homepage |
| GET | `/books` | BookController@index | Katalog buku |
| GET | `/books/{book}` | BookController@show | Detail buku |

### Authentication Routes
| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/login` | AuthenticatedSessionController@create | Form login |
| POST | `/login` | AuthenticatedSessionController@store | Proses login |
| GET | `/register` | RegisteredUserController@create | Form register |
| POST | `/register` | RegisteredUserController@store | Proses register |
| POST | `/logout` | AuthenticatedSessionController@destroy | Logout |

### Member Routes
| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/dashboard` | DashboardController@index | Dashboard member |
| GET | `/recommendations` | BookController@recommendations | Rekomendasi buku |
| GET | `/my-loans` | LoanController@index | Daftar peminjaman |
| GET | `/loans/{loan}` | LoanController@show | Detail peminjaman |
| POST | `/books/{book}/borrow` | LoanController@store | Request peminjaman |
| POST | `/loans/{loan}/renew` | LoanController@renew | Perpanjang peminjaman |
| POST | `/loans/{loan}/cancel` | LoanController@cancel | Cancel request |
| GET | `/my-reviews` | ReviewController@userReviews | Daftar review saya |
| POST | `/books/{book}/reviews` | ReviewController@store | Tulis review |
| PUT | `/reviews/{review}` | ReviewController@update | Update review |
| DELETE | `/reviews/{review}` | ReviewController@destroy | Hapus review |
| POST | `/reviews/{review}/vote` | ReviewController@vote | Vote review |
| DELETE | `/reviews/{review}/vote` | ReviewController@removeVote | Remove vote |
| GET | `/my-fines` | FineController@index | Daftar denda |
| POST | `/fines/{fine}/pay` | FineController@pay | Bayar denda |
| GET | `/notifications` | NotificationController@index | Daftar notifikasi |
| POST | `/notifications/{notification}/read` | NotificationController@markAsRead | Tandai sudah dibaca |

### Librarian Routes
| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/librarian/dashboard` | DashboardController@index | Dashboard pustakawan |
| GET | `/librarian/loans` | LoanController@index | Semua peminjaman |
| GET | `/librarian/loans/pending` | LoanController@pending | Request pending |
| GET | `/librarian/loans/awaiting-pickup` | LoanController@awaitingPickup | Menunggu pickup |
| GET | `/librarian/loans/active` | LoanController@active | Peminjaman aktif |
| GET | `/librarian/loans/{loan}` | LoanController@show | Detail peminjaman |
| POST | `/librarian/loans/{loan}/approve` | LoanController@approve | Approve peminjaman |
| POST | `/librarian/loans/{loan}/reject` | LoanController@reject | Reject peminjaman |
| POST | `/librarian/loans/{loan}/pickup` | LoanController@pickup | Konfirmasi pickup |
| POST | `/librarian/loans/{loan}/return` | LoanController@return | Proses pengembalian |
| GET | `/librarian/reviews` | ReviewController@index | Daftar review pending |
| GET | `/librarian/reviews/{review}` | ReviewController@show | Detail review |
| POST | `/librarian/reviews/{review}/approve` | ReviewController@approve | Approve review |
| POST | `/librarian/reviews/{review}/reject` | ReviewController@reject | Reject review |
| GET | `/librarian/fines` | FineController@index | Semua denda |
| GET | `/librarian/fines/unpaid` | FineController@unpaid | Denda belum bayar |
| POST | `/librarian/fines/{fine}/pay` | FineController@pay | Konfirmasi pembayaran |
| POST | `/librarian/fines/{fine}/waive` | FineController@waive | Waive denda |

### Admin Routes
| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/admin/dashboard` | DashboardController@index | Dashboard admin |
| GET | `/admin/books` | BookController@index | Daftar buku |
| GET | `/admin/books/create` | BookController@create | Form tambah buku |
| POST | `/admin/books` | BookController@store | Simpan buku baru |
| GET | `/admin/books/{book}` | BookController@show | Detail buku |
| GET | `/admin/books/{book}/edit` | BookController@edit | Form edit buku |
| PUT | `/admin/books/{book}` | BookController@update | Update buku |
| DELETE | `/admin/books/{book}` | BookController@destroy | Hapus buku |
| GET | `/admin/categories` | CategoryController@index | Daftar kategori |
| GET | `/admin/categories/create` | CategoryController@create | Form tambah kategori |
| POST | `/admin/categories` | CategoryController@store | Simpan kategori |
| GET | `/admin/categories/{category}/edit` | CategoryController@edit | Form edit kategori |
| PUT | `/admin/categories/{category}` | CategoryController@update | Update kategori |
| DELETE | `/admin/categories/{category}` | CategoryController@destroy | Hapus kategori |
| GET | `/admin/users` | UserController@index | Daftar user |
| GET | `/admin/users/create` | UserController@create | Form tambah user |
| POST | `/admin/users` | UserController@store | Simpan user baru |
| GET | `/admin/users/{user}` | UserController@show | Detail user |
| GET | `/admin/users/{user}/edit` | UserController@edit | Form edit user |
| PUT | `/admin/users/{user}` | UserController@update | Update user |
| DELETE | `/admin/users/{user}` | UserController@destroy | Hapus user |
| POST | `/admin/users/{user}/suspend` | UserController@suspend | Suspend user |
| POST | `/admin/users/{user}/activate` | UserController@activate | Activate user |

---

## 🧪 Testing

### Test Coverage

```
┌────────────────────────────────────────────────────────┐
│                   TEST RESULTS                          │
├────────────────────────────────────────────────────────┤
│  Tests:      156 passed (455 assertions)               │
│  Duration:   12.93s                                    │
│  Status:     ✅ ALL PASSING                            │
└────────────────────────────────────────────────────────┘
```

### Test Categories

| Test File | Tests | Description |
|-----------|-------|-------------|
| `AdminRoutesRenderViewsTest` | 18 | Test semua route admin render view dengan benar |
| `LibrarianRoutesRenderViewsTest` | 16 | Test semua route librarian render view dengan benar |
| `MemberRoutesRenderViewsTest` | 14 | Test semua route member render view dengan benar |
| `NoMissingViewsSmokeTest` | 5 | Smoke test untuk deteksi 500 error |
| `LoanWorkflowTest` | - | Test workflow peminjaman lengkap |
| `ReviewWorkflowTest` | - | Test workflow review dan voting |
| `AdminBookManagementTest` | - | Test CRUD buku oleh admin |
| `AuthenticationTest` | - | Test login, register, logout |
| `ProfileTest` | - | Test update profile user |

### Menjalankan Tests

```bash
# Jalankan semua tests
php artisan test

# Jalankan test spesifik
php artisan test --filter=AdminRoutesRenderViewsTest

# Dengan coverage report
php artisan test --coverage

# Parallel testing
php artisan test --parallel
```

---

## 📸 Screenshots

### 🏠 Homepage
- Hero section dengan search bar
- Featured books carousel
- Category quick links

### 📊 Dashboard Member
- Statistik peminjaman aktif (Active Loans)
- Denda yang belum dibayar (Unpaid Fines)
- Rekomendasi buku personal (AI-powered)
- Notifikasi terbaru
- Quick actions

### 📊 Dashboard Librarian
- Pending requests count
- Active loans count
- Overdue loans alert
- Quick actions untuk approve/process

### 📊 Dashboard Admin
- Total users, books, categories
- Active loans statistics
- System overview widgets
- User management shortcuts

### 📚 Katalog Buku
- Grid/list view toggle
- Search dengan autocomplete
- Filter by category
- Sort by popularity/rating/date

### 📖 Detail Buku
- Cover image & info
- Rating & review summary
- Availability status
- Borrow button
- Related books

---

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME="Perpustakaan Digital"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpusrpldb
DB_USERNAME=root
DB_PASSWORD=

# Mail (untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# Session
SESSION_DRIVER=database
```

---

## 📁 Struktur Folder

```
digital-library/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin controllers
│   │   │   ├── Auth/            # Authentication controllers
│   │   │   ├── Librarian/       # Librarian controllers
│   │   │   ├── BookController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── FineController.php
│   │   │   ├── LoanController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── ProfileController.php
│   │   │   └── ReviewController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Book.php
│   │   ├── Category.php
│   │   ├── Fine.php
│   │   ├── Loan.php
│   │   ├── Notification.php
│   │   ├── Recommendation.php
│   │   ├── Review.php
│   │   ├── ReviewVote.php
│   │   └── User.php
│   └── Services/
│       └── RecommendationService.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/
│       ├── auth/
│       ├── books/
│       ├── components/
│       ├── dashboard.blade.php
│       ├── fines/
│       ├── layouts/
│       ├── librarian/
│       ├── loans/
│       ├── notifications/
│       ├── profile/
│       ├── recommendations/
│       ├── reviews/
│       └── welcome.blade.php
├── routes/
│   ├── auth.php
│   └── web.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── composer.json
├── package.json
├── phpunit.xml
└── README.md
```

---

## 👥 Kontributor

<table>
  <tr>
    <td align="center">
      <a href="#">
        <img src="https://via.placeholder.com/100" width="100px;" alt=""/>
        <br />
        <sub><b>Developer</b></sub>
      </a>
      <br />
      <a href="#" title="Code">💻</a>
    </td>
  </tr>
</table>

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

```
MIT License

Copyright (c) 2026 Digital Library Project

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) - Role & permission management
- [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze) - Authentication starter kit
- [Alpine.js](https://alpinejs.dev/) - Lightweight JavaScript framework
- [Vite](https://vitejs.dev/) - Next generation frontend tooling

---

<p align="center">
  <strong>Made with ❤️ for Rekayasa Perangkat Lunak (RPL) Course</strong>
</p>

<p align="center">
  <a href="#-perpustakaan-digital-digital-library">⬆️ Back to Top</a>
</p>
