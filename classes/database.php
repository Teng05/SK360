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
    function getBarangays() {
        $conn = $this->openConnection();
        $stmt = $conn->query("SELECT * FROM barangays ORDER BY barangay_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Register youth and return verification code
    function registerYouth($first_name, $last_name, $email, $phone_number, $barangay_id, $password) {
        $conn = $this->openConnection();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users 
            (first_name, last_name, email, phone_number, barangay_id, password, role, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?, 'youth', 0)");
        $stmt->execute([$first_name, $last_name, $email, $phone_number, $barangay_id, $hashed_password]);

        $user_id = $conn->lastInsertId();

        $verification_code = random_int(100000, 999999);

        $stmt = $conn->prepare("INSERT INTO email_verifications 
            (user_id, verification_code, expires_at) 
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        $stmt->execute([$user_id, $verification_code]);

        return $verification_code;
    }

    // Check if email exists
    function emailExists($email) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() ? true : false;
    }

    public function loginUser($email, $password) {
        $conn = $this->openConnection();

        // Fetch user by email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if account is active
            if ($user['status'] !== 'active') {
                return false;
            }

            // Check if email is verified
            if (!$user['is_verified']) {
                return false;
            }

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Successful login
                return $user;
            }
        }

        // Invalid credentials
        return false;
    }
}