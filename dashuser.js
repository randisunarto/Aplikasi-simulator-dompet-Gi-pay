document.getElementById('topup-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var amount = document.getElementById('amount').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'dashuser.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                alert('Top-Up berhasil! Saldo baru Anda: Rp. ' + response.balance);
                document.querySelector('.saldo-info').innerHTML = '<i class="fas fa-wallet"></i> Saldo: ' + response.balance;
            } else {
                alert('Top-Up gagal: ' + response.message);
            }
        } else {
            alert('Terjadi kesalahan pada server.');
        }
    };

    xhr.send('action=topup&amount=' + amount);
});
