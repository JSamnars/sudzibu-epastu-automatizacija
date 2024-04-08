<?php
// E-pasta filtru un klasifikācijas loģika
function filterAndClassifyEmail($email) {
    $keywords = array("Jelgavas", "Rīgas", "Liepājas", "sudziba");
    $emailSubject = $email['subject'];
    $emailBody = $email['body'];

    foreach ($keywords as $keyword) {
        if (stripos($emailSubject, $keyword) !== false || stripos($emailBody, $keyword) !== false) {
            return $keyword;
        }
    }
}

// Biežāk uzdoto jautājumu un atbilžu bāze
$atbilzuDatubaze = array(
    "Jelgavas" => array(
        "atbilde" => "Lai atrisinātu ātrāk jūsu radušos problēmu lūgums sazināties ar mums par Jelgavas filiāli."
    ),
    "Rīgas" => array(
        "atbilde" => "Lai atrisinātu ātrāk jūsu radušos problēmu lūgums sazināties ar mums par Rīgas filiāli."
    ),
    "Liepājas" => array(
        "atbilde" => "Lai atrisinātu ātrāk jūsu radušos problēmu lūgums sazināties ar mums par Liepājas filiāli."
    )
);

// Automātiskās atbilžu ģenerēšanas loģika
function generateAutoResponse($email) {
    global $atbilzuDatubaze; 
    $emailClassification = filterAndClassifyEmail($email);
    if (isset($atbilzuDatubaze[$emailClassification])) {
        $autoResponse = $atbilzuDatubaze[$emailClassification];
        return $autoResponse;
    } else {
        return false;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>E-pasta Automatizētā Atbildētāja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">E-pasta Automatizētā Atbildētāja</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SCRIPT"]);?>">
            <div class="mb-3">
                <label for="email" class="form-label">E-pasta adrese:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="email_subject" class="form-label">E-pasta Temats:</label>
                <input type="text" class="form-control" id="email_subject" name="email_subject">
            </div>
            <div class="mb-3">
                <label for="email_body" class="form-label">E-pasta Saturs:</label>
                <textarea class="form-control" id="email_body" name="email_body" rows="3"></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Nosūtīt</button>
        </form>
    </div>

    <?php
    // PHP kods, kad forma ir nosūtīta
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = array(
            "subject" => $_POST["email_subject"],
            "body" => $_POST["email_body"],
            "to" => $_POST["email"]
        );

        $autoResponse = generateAutoResponse($email);

        // Nosūtīt automātisku atbildi uz lietotāja e-pastu
        $to = $email['to'];
        $subject = "Automātiskā atbilde";
        $message = "Atbilde: " . $autoResponse["atbilde"];
        $headers = "From: info@majaslapasizstrade.lv";

        if (mail($to, $subject, $message, $headers)) {
            echo "<div class='alert alert-success mt-3' role='alert'>Automātiskā atbilde nosūtīta uz Jūsu e-pasta adresi.</div>";
        } else {
            echo "<div class='alert alert-danger mt-3' role='alert'>Radās kļūda, automātiskā atbilde netika nosūtīta.</div>";
        }
    }
    ?>
</body>
</html>