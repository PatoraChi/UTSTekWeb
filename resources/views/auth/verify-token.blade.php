<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Kode | LahIya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex justify-content-center align-items-center vh-100">

  <div class="p-5 bg-secondary bg-opacity-25 rounded shadow" style="width: 100%; max-width: 400px;">


      <h3 class="text-center mb-4 fw-bold">Verifikasi Kode</h3>
      <p class="text-center text-secondary mb-4">Masukkan kode verifikasi yang dikirim ke email Anda.</p>

    <form action="{{ route('verify.check') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="token" class="form-label">Kode Verifikasi</label>
        <input type="text" id="token" name="token" class="form-control bg-dark text-light border-secondary" placeholder="Contoh: 123456" required>
        @if(session('error'))
        <div class="text-danger mt-2 small">{{ session('error') }}</div>
        @endif
    </div>

    <button id="verifyBtn" type="submit" class="btn btn-primary w-100">
        Verifikasi <span id="timer" class="ms-2 text-light small"></span>
    </button>

    <div class="text-center mt-3">
        <a href="{{ url('/forgot-password') }}" class="text-info text-decoration-none">
        <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    </form>

    @if (!empty($debug_code))
    <div class="alert alert-info mt-3">
        <strong>DEBUG:</strong> Kode OTP kamu: <code>{{ $debug_code }}</code>
    </div>
    @endif

  </div>

  <script>
    let timeLeft = 60;
    const timerDisplay = document.getElementById("timer");
    const verifyBtn = document.getElementById("verifyBtn");
    const resendWrapper = document.getElementById("resendWrapper");
    const verifyForm = document.getElementById("verifyForm");
    const errorMessage = document.getElementById("errorMessage");

    // Simulasi kode yang benar
    //const correctCode = "123456";

    // Timer countdown
    const countdown = setInterval(() => {
        if (timeLeft > 0) {
            timeLeft--;
            timerDisplay.textContent = `(${timeLeft}s)`;
        } else {
            clearInterval(countdown);
            verifyBtn.disabled = true;
            verifyBtn.classList.replace('btn-primary', 'btn-secondary');
            verifyBtn.textContent = 'Waktu habis!';
            resendWrapper.classList.remove('d-none');
        }
    }, 1000);

    // Saat user submit kode
    verifyForm.addEventListener("submit", function(e) {
        e.preventDefault(); // biar gak reload halaman

        const userCode = document.getElementById("token").value.trim();

        if (timeLeft <= 0) {
            alert("Waktu verifikasi sudah habis. Silakan kirim ulang kode.");
            return;
        }

        if (userCode === correctCode) {
            // arahkan ke halaman ubah password
            window.location.href = "{{ url('/reset-password') }}";
        } else {
            errorMessage.style.display = "block";
        }

    });
  </script>

</body>
</html>
