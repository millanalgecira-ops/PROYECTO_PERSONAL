<?php

// ══════════════════════════════════════════════
//  Modelo: tabla usuarios (Administrador/Cocina)
// ══════════════════════════════════════════════
class Usuario {
    private $conn;
    private $tabla = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function existeCorreo($email) {
        $sql  = "SELECT id FROM {$this->tabla} WHERE correo = :correo LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":correo", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function obtenerPorEmail($email) {
        $sql  = "SELECT * FROM {$this->tabla} WHERE correo = :correo AND activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":correo", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($datos) {
        try {
            $sql  = "INSERT INTO usuarios (nombre, correo, telefono, password_hash, rol_id, activo, creado_en)
                     VALUES (:nombre, :correo, :telefono, :password_hash, :rol_id, 1, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre",        $datos['nombre']);
            $stmt->bindParam(":correo",        $datos['correo']);
            $stmt->bindParam(":telefono",      $datos['telefono']);
            $stmt->bindParam(":password_hash", $datos['password']); // ya viene hasheado del controller
            $stmt->bindParam(":rol_id",        $datos['id_rol'], PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return "Error al registrar: " . $e->getMessage();
        }
    }

    public function obtenerTodos() {
        $sql  = "SELECT u.id, u.nombre, u.correo, u.rol_id, u.activo, u.creado_en, r.nombre AS rol_nombre
                 FROM usuarios u
                 LEFT JOIN roles r ON r.id = u.rol_id
                 ORDER BY u.creado_en DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM {$this->tabla} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla} SET nombre = :nombre, correo = :correo, rol_id = :rol_id";
            if (!empty($datos['password'])) {
                $sql .= ", password_hash = :password_hash";
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre", $datos['nombre']);
            $stmt->bindParam(":correo", $datos['correo']);
            $stmt->bindParam(":rol_id", $datos['id_rol'], PDO::PARAM_INT);
            $stmt->bindParam(":id",     $id, PDO::PARAM_INT);
            if (!empty($datos['password'])) {
                $stmt->bindParam(":password_hash", $datos['password']);
            }
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return "Error al actualizar: " . $e->getMessage();
        }
    }

    public function cambiarEstado($id, $activo) {
        try {
            $sql  = "UPDATE {$this->tabla} SET activo = :activo WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":activo", $activo, PDO::PARAM_INT);
            $stmt->bindParam(":id",     $id,     PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return "Error al cambiar estado: " . $e->getMessage();
        }
    }
}

// ══════════════════════════════════════════════
//  Modelo: tabla clientes (rol Cliente)
// ══════════════════════════════════════════════
class Cliente {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function existeCorreo($email) {
        $sql  = "SELECT id FROM clientes WHERE correo = :correo LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":correo", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function registrar($datos) {
        try {
            $sql  = "INSERT INTO clientes (nombre, correo, password_hash, activo, creado_en)
                     VALUES (:nombre, :correo, :password_hash, 1, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre",        $datos['nombre']);
            $stmt->bindParam(":correo",        $datos['correo']);
            $stmt->bindParam(":password_hash", $datos['password']); // ya viene hasheado del controller
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return "Error al registrar: " . $e->getMessage();
        }
    }

    public function obtenerPorEmail($email) {
        $sql  = "SELECT * FROM clientes WHERE correo = :correo AND activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":correo", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
