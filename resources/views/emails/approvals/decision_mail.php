<?php

$appName = config('app.name', 'HRM');
$payload = is_array($delivery->payload ?? null) ? $delivery->payload : [];
$decisionLabel = $delivery->decision?->label() ?? 'Cap nhat';
$decisionVerb = $delivery->decision?->mailVerb() ?? 'da duoc cap nhat';
$actionUrl = filled($delivery->action_url) ? url($delivery->action_url) : null;
$recipientName = $payload['recipient_name'] ?? $delivery->recipient_email;
$itemLabel = $payload['item_label'] ?? 'Yeu cau';
$reviewerName = $payload['reviewer_name'] ?? 'He thong';
$reviewNote = $payload['review_note'] ?? null;
$decisionAt = $payload['decision_at'] ?? null;
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($delivery->subject) ?></title>
</head>
<body style="margin:0; padding:24px 0; background:#f3f6fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 16px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">
                                <tr>
                                    <td style="padding:28px 32px; background:linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color:#ffffff;">
                                        <div style="font-size:13px; letter-spacing:1.2px; text-transform:uppercase; opacity:0.9;"><?= $escape($appName) ?></div>
                                        <div style="margin-top:10px; font-size:28px; font-weight:700; line-height:1.3;"><?= $escape($decisionLabel) ?></div>
                                        <div style="margin-top:8px; font-size:15px; opacity:0.92;"><?= $escape($itemLabel) ?> cua ban <?= $escape($decisionVerb) ?>.</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:28px 32px 12px;">
                                        <p style="margin:0 0 12px; font-size:16px;">Xin chao <strong><?= $escape($recipientName) ?></strong>,</p>
                                        <p style="margin:0; font-size:15px; line-height:1.7; color:#475569;">
                                            He thong ghi nhan rang <strong><?= $escape($itemLabel) ?></strong> cua ban <?= $escape($decisionVerb) ?> boi
                                            <strong><?= $escape($reviewerName) ?></strong>.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 32px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate; border-spacing:0; background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px;">
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b; width:180px;">Loai yeu cau</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;"><?= $escape($itemLabel) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Ket qua</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;"><?= $escape($decisionLabel) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Nguoi xu ly</td>
                                                <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;"><?= $escape($reviewerName) ?></td>
                                            </tr>
                                            <?php if ($decisionAt): ?>
                                                <tr>
                                                    <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#64748b;">Thoi gian</td>
                                                    <td style="padding:18px 20px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:600; color:#0f172a;"><?= $escape($decisionAt) ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td style="padding:18px 20px; font-size:14px; color:#64748b;">Ghi chu</td>
                                                <td style="padding:18px 20px; font-size:15px; font-weight:600; color:#0f172a;"><?= $escape($reviewNote ?: 'Khong co ghi chu bo sung.') ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <?php if ($actionUrl): ?>
                                    <tr>
                                        <td style="padding:8px 32px 0;">
                                            <a href="<?= $escape($actionUrl) ?>" style="display:inline-block; padding:12px 18px; border-radius:12px; background:#1d4ed8; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700;">
                                                Mo lai man hinh lien quan
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding:24px 32px 32px; font-size:13px; line-height:1.7; color:#64748b;">
                                        Email nay duoc gui tu dong tu he thong <strong><?= $escape($appName) ?></strong>.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
