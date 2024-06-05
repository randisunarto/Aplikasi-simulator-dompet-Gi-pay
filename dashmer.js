document.addEventListener('DOMContentLoaded', () => {
    const tarikSaldoBtn = document.getElementById('tarik-saldo-btn');
    const tarikSaldoModal = document.getElementById('tarik-saldo-modal');
    const konfirmasiModal = document.getElementById('konfirmasi-modal');
    const hasilModal = document.getElementById('hasil-modal');
    const closeBtns = document.querySelectorAll('.close-btn');
    const transferBtn = document.getElementById('transfer-btn');
    const konfirmasiBtn = document.getElementById('konfirmasi-btn');
    const jumlahKonfirmasi = document.getElementById('jumlah-konfirmasi');
    const rekeningKonfirmasi = document.getElementById('rekening-konfirmasi');
    const hasilPesan = document.getElementById('hasil-pesan');

    const navbarItems = document.querySelectorAll('.navbar-center a');

    navbarItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            gsap.to(item, { scale: 1.1, duration: 0.3 });
        });

        item.addEventListener('mouseleave', () => {
            gsap.to(item, { scale: 1, duration: 0.3 });
        });
    });

    // Function to open modal
    const openModal = (modal) => {
        modal.style.display = 'flex';
    };

    // Function to close modal
    const closeModal = (modal) => {
        modal.style.display = 'none';
    };

    // Event listener for "Tarik Saldo" button
    tarikSaldoBtn.addEventListener('click', () => {
        openModal(tarikSaldoModal);
    });

    // Event listener for "Transfer" button
    transferBtn.addEventListener('click', () => {
        const bankAccountSelect = document.getElementById('bank-account');
        const bankAccount = bankAccountSelect.options[bankAccountSelect.selectedIndex].text;
        const jumlahTarik = document.getElementById('jumlah-tarik').value;
        const jumlahTarikNumber = parseFloat(jumlahTarik);

        if (jumlahTarikNumber > 0) {
            jumlahKonfirmasi.textContent = `Rp. ${jumlahTarikNumber}`;
            rekeningKonfirmasi.textContent = bankAccount;

            closeModal(tarikSaldoModal);
            openModal(konfirmasiModal);
        } else {
            alert('Jumlah yang ditarik harus lebih dari 0');
        }
    });

    // Event listener for "Konfirmasi" button
    konfirmasiBtn.addEventListener('click', () => {
        const jumlahTarik = parseFloat(document.getElementById('jumlah-tarik').value);

        fetch('withdraw.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                jumlah: jumlahTarik,
                bank: document.getElementById('bank-account').value
            })
        })
        .then(response => response.json())
        .then(data => {
            hasilPesan.textContent = data.message;
            if (data.success) {
                const saldoElement = document.querySelector('.saldo-section h3');
                saldoElement.textContent = 'Saldo Toko: Rp. ' + parseFloat(data.new_balance).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            closeModal(konfirmasiModal);
            openModal(hasilModal);
        })
        .catch(error => {
            hasilPesan.textContent = 'Terjadi kesalahan saat menarik saldo. Coba lagi.';
            closeModal(konfirmasiModal);
            openModal(hasilModal);
        });
    });

    // Event listeners for close buttons
    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal');
            closeModal(modal);
        });
    });

    // Close modal when clicking outside the modal content
    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target);
        }
    });
});

