<?php
class Database {

    private $host = 'localhost';
    private $dbname = 'sk360_db';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function openConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function closeConnection() {
        $this->conn = null;
    }

    // Fetch barangays
    public function getBarangays() {
        $conn = $this->openConnection();
        $stmt = $conn->query("SELECT * FROM barangays ORDER BY barangay_name");
        return $stmt->fetchAll();
    }

    // Register youth and return both user_id and verification_code
    public function registerYouth($first_name, $last_name, $email, $phone_number, $barangay_id, $password) {
        $conn = $this->openConnection();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users
            (first_name, last_name, email, phone_number, barangay_id, password, role, is_verified, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'youth', 0, 'inactive', NOW())
        ");
        $stmt->execute([
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $barangay_id,
            $hashed_password
        ]);

        $user_id = $conn->lastInsertId();
        $verification_code = random_int(100000, 999999);

        $stmt = $conn->prepare("
            INSERT INTO email_verifications
            (user_id, verification_code, expires_at, created_at)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW())
        ");
        $stmt->execute([$user_id, $verification_code]);

        return [
            'user_id' => $user_id,
            'verification_code' => $verification_code
        ];
    }

    // Check if email exists
    public function emailExists($email) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() ? true : false;
    }

    public function loginUser($email, $password) {
        $conn = $this->openConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['status'] !== 'active') {
                return false;
            }

            if (!$user['is_verified']) {
                return false;
            }

            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }
// Top bar name
    public function getUserById($user_id) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, role FROM users WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    // CREATE SLOT
public function createSlot($type, $title, $desc, $role, $start, $end) {
    $conn = $this->openConnection();

    $stmt = $conn->prepare("
        INSERT INTO submission_slots 
        (submission_type, title, description, role, start_date, end_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([$type, $title, $desc, $role, $start, $end]);
}

// GET ALL SLOTS
public function getSlots() {
    $conn = $this->openConnection();

    $stmt = $conn->query("SELECT * FROM submission_slots ORDER BY slot_id DESC");
    return $stmt->fetchAll();
}

// DELETE SLOT
public function deleteSlot($id) {
    $conn = $this->openConnection();

    $stmt = $conn->prepare("DELETE FROM submission_slots WHERE slot_id = ?");
    return $stmt->execute([$id]);
}

//Event calendar
// CREATE EVENT
public function createEvent($title, $event_type, $start, $end, $description = null, $created_by = null) {
    $conn = $this->openConnection();

    $stmt = $conn->prepare("
        INSERT INTO events 
        (title, description, event_type, start_datetime, end_datetime, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $title,
        $description,
        $event_type,
        $start,
        $end,
        $created_by
    ]);
}

// GET ALL EVENTS
public function getEvents() {
    $conn = $this->openConnection();

    $stmt = $conn->query("
        SELECT event_id, title, event_type, start_datetime, end_datetime
        FROM events
        ORDER BY start_datetime ASC
    ");

    return $stmt->fetchAll();
}

// GET UPCOMING EVENTS
public function getUpcomingEvents($limit = 5) {
    $conn = $this->openConnection();

    $stmt = $conn->prepare("
        SELECT event_id, title, event_type, start_datetime, end_datetime, description
        FROM events
        WHERE DATE(start_datetime) >= CURDATE()
        ORDER BY start_datetime ASC
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

// DELETE EVENT
public function deleteEvent($event_id) {
    $conn = $this->openConnection();

    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    return $stmt->execute([$event_id]);
}

}