<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="row" style="display: flex; gap: 30px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 400px;">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-terminal"></i> H2H Bank Jateng Simulator</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <p>Gunakan simulator ini untuk menguji endpoint Authorization, Inquiry, dan Payment secara lokal.</p>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label><strong>1. Get Token (Authorization)</strong></label>
                    <button id="btn-auth" class="btn btn-primary" style="margin-top: 5px; width: 100%;">Test Auth (Basic Auth)</button>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label><strong>2. Inquiry Tagihan</strong></label>
                    <input type="text" id="inquiry-norm" class="form-control" value="2304002648" placeholder="No RM Pasien" style="margin-top: 5px; margin-bottom: 5px;">
                    <button id="btn-inquiry" class="btn btn-info" style="width: 100%; color: #fff;">Test Inquiry</button>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label><strong>3. Payment / Posting</strong></label>
                    <input type="text" id="payment-norm" class="form-control" value="2304002648" placeholder="No RM" style="margin-top: 5px; margin-bottom: 5px;">
                    <input type="number" id="payment-total" class="form-control" value="270000" placeholder="Total Tagihan" style="margin-bottom: 5px;">
                    <input type="text" id="payment-noreff" class="form-control" value="REF<?= time() ?>" placeholder="No Reff Bank" style="margin-bottom: 5px;">
                    <button id="btn-payment" class="btn btn-success" style="width: 100%;">Test Payment</button>
                </div>
            </div>
        </div>
    </div>

    <div style="flex: 1; min-width: 400px;">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-code"></i> Response Output</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <pre id="api-output" style="background: #1e1e1e; color: #4ec9b0; padding: 15px; border-radius: 8px; min-height: 300px; max-height: 450px; overflow: auto; font-size: 0.9rem;"></pre>
            </div>
        </div>
    </div>
</div>

<script>
let currentApiKey = '';

document.getElementById('btn-auth').addEventListener('click', async () => {
    const output = document.getElementById('api-output');
    output.textContent = 'Loading Auth...';
    try {
        const res = await fetch('<?= base_url('h2h/auth') ?>', {
            method: 'GET',
            headers: {
                'Authorization': 'Basic ' + btoa('bankjateng:puskesmas123')
            }
        });
        const data = await res.json();
        if (data['x-api-key']) {
            currentApiKey = data['x-api-key'];
        }
        output.textContent = JSON.stringify(data, null, 2);
    } catch (err) {
        output.textContent = 'Error: ' + err.message;
    }
});

document.getElementById('btn-inquiry').addEventListener('click', async () => {
    const output = document.getElementById('api-output');
    const norm = document.getElementById('inquiry-norm').value;
    output.textContent = 'Loading Inquiry...';
    try {
        const res = await fetch('<?= base_url('h2h/inquiry') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'x-api-key': currentApiKey
            },
            body: JSON.stringify({ no_rm: norm })
        });
        const data = await res.json();
        output.textContent = JSON.stringify(data, null, 2);
    } catch (err) {
        output.textContent = 'Error: ' + err.message;
    }
});

document.getElementById('btn-payment').addEventListener('click', async () => {
    const output = document.getElementById('api-output');
    const norm = document.getElementById('payment-norm').value;
    const total = document.getElementById('payment-total').value;
    const noreff = document.getElementById('payment-noreff').value;
    
    output.textContent = 'Loading Payment...';
    try {
        const res = await fetch('<?= base_url('h2h/payment') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'x-api-key': currentApiKey
            },
            body: JSON.stringify({
                no_rm: norm,
                total_tagihan: total,
                tgl_transaksi: '<?= date('Ymd') ?>',
                channel: '6010',
                device: 'W099001',
                noreff: noreff
            })
        });
        const data = await res.json();
        output.textContent = JSON.stringify(data, null, 2);
    } catch (err) {
        output.textContent = 'Error: ' + err.message;
    }
});
</script>

<?= $this->endSection() ?>
