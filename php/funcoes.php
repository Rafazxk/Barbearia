<?php
function horariosPadrao() {
    return ["08:00", "09:00", "10:00", "11:00", "12:00",
            "13:00", "14:00", "15:00", "16:00", "17:00",
            "18:00", "19:00", "20:00"];
}

function horariosConfigurados($conn, $barbeiro_id, $data) {
    $dia_semana = date("N", strtotime($data)); // 1 = segunda, ..., 7 = domingo

    // Segunda-feira fechada por padrão
    if ($dia_semana == 1) return [];

    $stmt = $conn->prepare("SELECT abrir, horario_abertura, horario_fechamento FROM configuracoes_barbeiro WHERE barbeiro_id = ? AND data = ?");
    $stmt->execute([$barbeiro_id, $data]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($config) {
        if ($config["abrir"] == 0) return [];
        return array_filter(horariosPadrao(), fn($h) => $h >= $config["horario_abertura"] && $h <= $config["horario_fechamento"]);
    }

    // Sem config = segue padrão
    return horariosPadrao();
}