{{-- resources/views/payment/checkout.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Midtrans Snap</title>
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        #pay-button { 
            padding: 15px 30px; 
            font-size: 1.2em; 
            background-color: #008CBA; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>Detail Pesanan Anda</h1>
    <p>Total Pembayaran: **Rp 55.000**</p>
    
    <button id="pay-button">Bayar Sekarang dengan Midtrans</button>
    <div id="payment-status" style="margin-top: 20px; color: green; font-weight: bold;"></div>

    <script type="text/javascript">
      document.getElementById('pay-button').onclick = function(){
        document.getElementById('payment-status').innerText = 'Memproses...';
        
        // 1. Panggil Endpoint Controller untuk mendapatkan Snap Token via AJAX
        fetch('{{ route('payment.create') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel CSRF Token
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}) // Kirim data yang dibutuhkan jika ada
        })
        .then(response => response.json())
        .then(data => {
            if (data.snap_token) {
                document.getElementById('payment-status').innerText = 'Snap Pop-up Siap...';
                
                // 2. Buka Pop-up Pembayaran Midtrans Snap
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        // User menyelesaikan pembayaran
                        document.getElementById('payment-status').innerText = "Pembayaran Berhasil! Order ID: " + data.order_id;
                        alert("Pembayaran Berhasil!");
                        // Arahkan ke halaman terima kasih/status
                        // window.location.href = '/order/status/' + data.order_id;
                    },
                    onPending: function(result){
                        // Pembayaran masih menunggu (e.g., VA belum dibayar)
                        document.getElementById('payment-status').innerText = "Menunggu Pembayaran. Order ID: " + data.order_id;
                        alert("Menunggu pembayaran Anda.");
                    },
                    onError: function(result){
                        // Terjadi error di Snap
                        document.getElementById('payment-status').innerText = "Pembayaran Gagal!";
                        alert("Pembayaran Gagal!");
                    },
                    onClose: function(){
                        // User menutup pop-up sebelum menyelesaikan
                        document.getElementById('payment-status').innerText = "Pop-up Ditutup. Silakan coba lagi.";
                        alert('Anda menutup pop-up tanpa menyelesaikan pembayaran.');
                    }
                });
            } else {
                document.getElementById('payment-status').innerText = 'Gagal mendapatkan Snap Token.';
                alert('Gagal mendapatkan Snap Token.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('payment-status').innerText = 'Terjadi kesalahan sistem.';
            alert('Terjadi kesalahan saat memproses pembayaran.');
        });
      };
    </script>

</body>
</html>