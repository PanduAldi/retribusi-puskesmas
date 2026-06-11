<?php

 if ($store['sign_type'] === "tte") {
            $validation = $this->validation;
            $validation->setRules(['passphrase' => 'required']);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Passphrase wajib diisi.']);
            }

            $nik = $penandatangan['nik'];
            $passphrase = $this->request->getVar('passphrase');

            // Validasi passphrase

            // 3. Buat PDF menggunakan Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // 4. Simpan ke Temporary Kartu Kuning
            $outputFolder = WRITEPATH . 'uploads/temp_kartu_kuning/';
            if (!is_dir($outputFolder)) {
                mkdir($outputFolder, 0777, true);
            }

            $fileName = 'AK1-' . $store['uuid'] . '-' . url_title($pencaker['nama'], '-', true) . '.pdf';
            $outputPath = $outputFolder . $fileName;
            file_put_contents($outputPath, $dompdf->output());


            // 5. posting sign dokumen ke Api e-sign 
            $esignUrl = getenv('esign.url');
            $esignUser = getenv('esign.username');
            $esignPass = getenv('esign.password');

            $fileNameSign = str_replace('.pdf', '_sign.pdf', $fileName);
            $file = new \CURLFile($outputPath, mime_content_type($outputPath), $fileNameSign);

            $dataset = [
                "file"       => $file,
                "nik"        => $nik,
                "passphrase" => $passphrase,
                "tampilan"   => "invisible",
                "reason"     => 'Dokumen Ini Telah Disetujui dengan Tanda Tangan Elektronik',
                "location"   => 'Brebes'
            ];

            $ch = curl_init($esignUrl . '/api/sign/pdf');

            /* Set JSON data to POST */
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataset);

            /* Set user password */
            curl_setopt($ch, CURLOPT_USERPWD, "$esignUser:$esignPass");

            /* Return json */
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            // Optional: Header function if needed for debugging, but not strictly used here
            $headers = [];
            curl_setopt(
                $ch,
                CURLOPT_HEADERFUNCTION,
                function ($cl, $header) use (&$headers) {
                    $len = strlen($header);
                    $header_part = explode(':', $header, 2);
                    if (count($header_part) < 2) return $len;
                    $headers[strtolower(trim($header_part[0]))][] = trim($header_part[1]);
                    return $len;
                }
            );

            $body = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                log_message('error', "E-Sign cURL Error: " . $curlError);

                // Log TTE Gagal (CURL Error)
                $this->tteLogModel->insert([
                    'kartu_pengantar_id' => $store['id'],
                    'nik'                => $nik,
                    'status'             => 'GAGAL',
                    'status_code'        => 0,
                    'error_message'      => "cURL Error: " . $curlError,
                    'response_payload'   => null
                ]);

                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => "cURL Error: " . $curlError]);
            }

            if ($statusCode == 200) {
                $signedFolder = WRITEPATH . 'uploads/kartu_kuning/';
                if (!is_dir($signedFolder)) {
                    mkdir($signedFolder, 0777, true);
                }

                $fileNameSign = str_replace('.pdf', '_sign.pdf', $fileName);
                $signedPath = $signedFolder . $fileNameSign;

                // Cek jika body berisi JSON (biasanya meta) atau langsung binary PDF
                $jsonRes = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($jsonRes['file'])) {
                    file_put_contents($signedPath, base64_decode($jsonRes['file']));
                } else {
                    // Jika langsung binary PDF
                    file_put_contents($signedPath, $body);
                }

                // Hapus file temp
                if (file_exists($outputPath)) {
                    unlink($outputPath);
                }

                $fileName = $fileNameSign; // Gunakan file signed untuk DB

                // Log TTE Berhasil
                $this->tteLogModel->insert([
                    'kartu_pengantar_id' => $store['id'],
                    'nik'                => $nik,
                    'status'             => 'BERHASIL',
                    'status_code'        => $statusCode,
                    'error_message'      => null,
                    'response_payload'   => (strlen($body) > 1000) ? "PDF BINARY DATA (Length: " . strlen($body) . ")" : $body
                ]);
            }
 };