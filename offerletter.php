<?php
include 'connect.php';
?>

<?php
$showModal = false;
$offerEmail = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showModal = true;

    $full_name = $_POST['full_name'];
    $preferred_university = $_POST['preferred_university'];
    $program_type = $_POST['program_type'];
    $preferred_subject = $_POST['preferred_subject'];
    $intake_season = $_POST['intake_season'];
    $intake_year = $_POST['intake_year'];

    $offerEmail = "Dear Admissions Committee,\n\n";
    $offerEmail .= "We would like to submit the application of $full_name for the $program_type program in $preferred_subject at $preferred_university.\n";
    $offerEmail .= "The applicant wishes to enroll in the $intake_season $intake_year intake.\n\n";
    $offerEmail .= "All required academic documents and personal information have been provided.\n\n";
    $offerEmail .= "Kind regards,\nGlobalEdX Admission Team";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Get Offer Letter</title>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

* {
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

  body {
    background: linear-gradient(135deg,rgb(71, 81, 255),rgb(0, 10, 149));
    margin: 0;
    padding: 50px 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    perspective: 1400px;
    overflow-x: hidden;
  }

  .form-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: saturate(180%) blur(16px);
    border-radius: 30px;
    padding: 50px 60px;
    max-width: 900px;
    width: 100%;
    box-shadow:
      0 20px 50px rgba(0, 0, 0, 0.25),
      inset 0 0 20px rgba(108, 99, 255, 0.25);
    transform-style: preserve-3d;
    animation: cardEntrance 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    transform-origin: center bottom;
    border: 1.5px solid rgba(108, 99, 255, 0.4);
  }

  h2 {
    text-align: center;
    margin-bottom: 50px;
    color: #1b1b3a;
    font-weight: 800;
    font-size: 3rem;
    letter-spacing: 2px;
    text-shadow:
      0 2px 8px rgba(108, 99, 255, 0.55),
      0 0 12px rgba(108, 99, 255, 0.4);
  }

  label {
    font-weight: 700;
    display: block;
    margin-top: 25px;
    color: #2a2a50;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    user-select: none;
    transition: color 0.3s ease;
  }

  label:hover {
    color: #6c63ff;
  }

  input, select, textarea {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #cfd7ff;
    border-radius: 18px;
    margin-top: 10px;
    font-size: 1.1rem;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:
      inset 0 4px 10px rgba(0, 0, 0, 0.05),
      0 2px 8px rgba(108, 99, 255, 0.1);
    background: #fefeff;
    font-weight: 500;
    transform-style: preserve-3d;
    color: #2a2a50;
    letter-spacing: 0.02em;
  }

  input::placeholder,
  textarea::placeholder {
    color: #a2a6c6;
    font-weight: 400;
  }

  input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #6c63ff;
    box-shadow:
      0 0 25px rgba(108, 99, 255, 0.6),
      inset 0 0 12px rgba(108, 99, 255, 0.3);
    background: #fff;
    color: #1b1b3a;
    transform: translateZ(40px);
  }

  .file-input {
    padding: 8px 0;
  }

  .submit-btn {
    background: linear-gradient(135deg, #6c63ff, #5549d5);
    color: #fff;
    border: none;
    padding: 20px;
    width: 100%;
    border-radius: 20px;
    margin-top: 50px;
    font-size: 1.4rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow:
      0 12px 30px rgba(108, 99, 255, 0.75),
      0 8px 10px rgba(0, 0, 0, 0.25);
    transition: all 0.35s ease;
    transform-style: preserve-3d;
    letter-spacing: 0.08em;
    user-select: none;
    text-transform: uppercase;
  }

  .submit-btn:hover {
    background: linear-gradient(135deg, #5549d5, #3f3999);
    box-shadow:
      0 18px 40px rgba(85, 73, 213, 0.95),
      0 12px 15px rgba(0, 0, 0, 0.35);
    transform: translateZ(14px) scale(1.02);
  }

  .submit-btn:active {
    transform: translateZ(8px) scale(0.98);
    box-shadow:
      0 10px 22px rgba(85, 73, 213, 0.7),
      0 6px 8px rgba(0, 0, 0, 0.2);
  }

  /* Subtle floating effect on input focus */
  input:focus, select:focus, textarea:focus {
    animation: floatUp 0.6s forwards;
  }

  @keyframes floatUp {
    0% {
      transform: translateZ(0);
    }
    100% {
      transform: translateZ(40px);
    }
  }

  @keyframes cardEntrance {
    0% {
      opacity: 0;
      transform: rotateX(18deg) translateY(60px) scale(0.94);
      filter: blur(10px);
    }
    100% {
      opacity: 1;
      transform: rotateX(0deg) translateY(0) scale(1);
      filter: blur(0);
    }
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .form-card {
      padding: 35px 30px;
      border-radius: 20px;
    }
    h2 {
      font-size: 2.25rem;
      margin-bottom: 35px;
    }
    label {
      font-size: 1rem;
    }
    input, select, textarea {
      font-size: 1rem;
      padding: 14px 16px;
      border-radius: 14px;
    }
    .submit-btn {
      font-size: 1.15rem;
      padding: 16px;
      border-radius: 16px;
      margin-top: 35px;
    }
  }
  .file-input {
  margin-top: 20px;
}

input[type="file"] {
  padding: 14px 18px;
  font-size: 1.05rem;
  font-weight: 600;
  border: 2px solid #ccc;
  border-radius: 14px;
  background: #fafafa;
  color: #333;
  width: 100%;
  cursor: pointer;
  transition: all 0.3s ease;
}

input[type="file"]::file-selector-button {
  font-size: 1.05rem;
  font-weight: 600;
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #6c63ff, #5549d5);
  color: white;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.2s ease;
  box-shadow: 0 4px 10px rgba(108, 99, 255, 0.4);
}

input[type="file"]::file-selector-button:hover {
  background: linear-gradient(135deg, #5549d5, #3f3999);
  transform: scale(1.05);
}
.typing-text {
  text-align: center;
  font-weight: 700;
  font-size: 2rem;
  color: #222;
  margin-bottom: 40px;
}

.typing-text span::after {
  content: "|";
  animation: blink 0.8s infinite;
  color: #6c63ff;
  font-weight: bold;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}


</style>

</head>