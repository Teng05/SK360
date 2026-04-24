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
    public function isEmailExists($email) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() ? true : false;
    }

    // Check if phone number exists
    public function isPhoneExists($phone_number) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE phone_number = ? LIMIT 1");
        $stmt->execute([$phone_number]);
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


    public function getUserById($user_id) {
        $conn = $this->openConnection();
        // Idagdag ang barangay_id sa SELECT statement
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, phone_number, role, barangay_id FROM users WHERE user_id = ? LIMIT 1");
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
        
        // 1. I-prepare ang SQL query gamit ang named placeholder (:limit)
        $stmt = $conn->prepare("SELECT * FROM events WHERE end_datetime >= NOW() ORDER BY start_datetime ASC LIMIT :limit");
        
        // 2. I-bind ang value at siguraduhing PDO::PARAM_INT ang gamit
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        
        // 3. I-execute nang walang array sa loob
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // DELETE EVENT
    public function deleteEvent($event_id) {
        $conn = $this->openConnection();

        $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
        return $stmt->execute([$event_id]);
    }

    // --- ADD THESE TO YOUR DATABASE CLASS ---

    // 1. Fetch the single most recent announcement
    public function getLatestAnnouncement() {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    // 3. Fetch Barangay Rank from your rankings table
    public function getBarangayRank($barangay_id) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT ranking_id FROM rankings WHERE barangay_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$barangay_id]);
        $rank = $stmt->fetch();
        return $rank ? "#" . $rank['ranking_id'] : "N/A";
    }

    public function getAllAnnouncements() {
        try {
            // We fetch the latest announcements first using ORDER BY created_at DESC
            $sql = "SELECT * FROM announcements ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If there's an error, return an empty array so the page doesn't crash
            return [];
        }
    }

    // --- ADD THESE TO YOUR DATABASE CLASS ---

    public function getUserByEmailOrPhone($identifier) {
        $conn = $this->openConnection();
        // Your SQL uses email and phone_number
        $stmt = $conn->prepare("SELECT user_id, email, phone_number FROM users WHERE email = ? OR phone_number = ? LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        return $stmt->fetch();
    }

    public function storeResetCode($user_id, $code, $method) {
        $conn = $this->openConnection();
        
        // Clean up old requests for this user
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Insert into your password_resets table
        $stmt = $conn->prepare("
            INSERT INTO password_resets (user_id, reset_code, method, expires_at, created_at) 
            VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW())
        ");
        return $stmt->execute([$user_id, $code, $method]);
    }

    // Updated to use the correct connection method and the password_resets table
    public function storeResetToken($user_id, $token, $method) {
        $conn = $this->openConnection();
        
        // Clean up old codes/tokens for this user
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Insert the long token instead of a 6-digit code
        $stmt = $conn->prepare("
            INSERT INTO password_resets (user_id, reset_token, method, expires_at, created_at) 
            VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW())
        ");
        return $stmt->execute([$user_id, $token, $method]);
    }

    public function getUserByToken($token) {
        $conn = $this->openConnection();
        // Join with users table so we get the user's info immediately
        $stmt = $conn->prepare("
            SELECT u.* FROM users u 
            JOIN password_resets p ON u.user_id = p.user_id 
            WHERE p.reset_token = ? AND p.expires_at > NOW() 
            LIMIT 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    // You also need this to actually save the new password later
    public function updatePassword($user_id, $new_password) {
        $conn = $this->openConnection();
        
        // Hash the password for security
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute([$hashed_password, $user_id]);
    }

    // Inside classes/database.php

    public function getUserByEmail($email) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT user_id, first_name FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function saveResetCode($user_id, $code, $method = 'email') {
        $conn = $this->openConnection();
        // Using DATE_ADD(NOW()...) here fixes the timezone issues we discussed
        $sql = "INSERT INTO password_resets (user_id, reset_code, method, expires_at) 
                VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$user_id, $code, $method]);
    }

    public function verifyResetCode($code) {
        $conn = $this->openConnection();
        
        // Check if code exists and is not expired
        $sql = "SELECT user_id, reset_id FROM password_resets 
                WHERE reset_code = ? AND expires_at > NOW() LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$code]);
        $reset = $stmt->fetch();

        if ($reset) {
            // Delete the code immediately so it can't be used again
            $del = $conn->prepare("DELETE FROM password_resets WHERE reset_id = ?");
            $del->execute([$reset['reset_id']]);
            
            return $reset; // Returns array with user_id and reset_id
        }
        
        return false;
    }

    // Kunin ang lahat ng events para sa calendar view
    public function getAllEvents() {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("SELECT * FROM events ORDER BY start_datetime ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCouncilByBarangay($barangay_id) {
        $conn = $this->openConnection();
        try {
            $sql = "
                /* Part 1: Kunin ang Chairman at Secretary mula sa users table */
                SELECT 
                    CONCAT(first_name, ' ', last_name) AS name, 
                    CASE 
                        WHEN role = 'sk_chairman' THEN 'SK Chairman'
                        WHEN role = 'sk_secretary' THEN 'SK Secretary'
                    END AS position, 
                    email, 
                    phone_number AS phone, 
                    '2024-2026' AS term 
                FROM users 
                WHERE barangay_id = :bid AND role IN ('sk_chairman', 'sk_secretary')
                
                UNION ALL
                
                /* Part 2: Kunin ang Treasurer at Councilors mula sa sk_council table */
                SELECT name, position, email, phone, term 
                FROM sk_council 
                WHERE barangay_id = :bid
                
                ORDER BY 
                    CASE 
                        WHEN position = 'SK Chairman' THEN 1
                        WHEN position = 'SK Secretary' THEN 2
                        WHEN position = 'SK Treasurer' THEN 3
                        ELSE 4 
                    END ASC, name ASC";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':bid', $barangay_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getCouncilByBarangay: " . $e->getMessage());
            return [];
        }
    }

    // 2. Kunin ang pangalan ng Barangay para sa header ng page
    public function getBarangayName($barangay_id) {
        $conn = $this->openConnection();
        try {
            // Tandaan: Siguraduhin na 'barangay_id' ang column name sa table mo
            $stmt = $conn->prepare("SELECT barangay_name FROM barangays WHERE barangay_id = ?");
            $stmt->execute([$barangay_id]);
            $res = $stmt->fetch();
            return $res ? $res['barangay_name'] : 'Unknown Barangay';
        } catch (PDOException $e) {
            return 'Unknown Barangay';
        }
    }

    // Update user profile information
    public function updateUserProfile($user_id, $first_name, $last_name, $email, $phone_number) {
        $conn = $this->openConnection();
        $stmt = $conn->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, phone_number = ? 
            WHERE user_id = ?
        ");
        return $stmt->execute([$first_name, $last_name, $email, $phone_number, $user_id]);
    }

    public function updateProfilePic($user_id, $filename) {
        try {
            $query = "UPDATE users SET profile_pic = :profile_pic WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':profile_pic', $filename);
            $stmt->bindParam(':id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update Profile Pic Error: " . $e->getMessage());
            return false;
        }
    }

}