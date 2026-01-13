<?php

namespace guillaumepaquin\factuhello\model;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Classe pour la gestion des envois d'emails
 */
class MailModel {
    private const CONFIG_PATH = __DIR__ . '/../../../mail.config.ini';

    private static ?PHPMailer $mailer = null;
    private static ?array $config = null;

    /**
     * Initialise et retourne l'instance PHPMailer configurée
     */
    private static function getMailer(): PHPMailer {
        $config = self::getConfig();

        if (self::$mailer === null) {
            self::$mailer = new PHPMailer(true);

            // Configuration SMTP
            self::$mailer->isSMTP();
            self::$mailer->Host = $config['SMTP_HOST'];
            self::$mailer->SMTPAuth = true;
            self::$mailer->Username = $config['SMTP_USERNAME'];
            self::$mailer->Password = $config['SMTP_PASSWORD'];
            self::$mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            self::$mailer->Port = (int) $config['SMTP_PORT'];
            self::$mailer->CharSet = 'UTF-8';

            // Expéditeur par défaut
            self::$mailer->setFrom($config['SMTP_FROM_EMAIL'], $config['SMTP_FROM_NAME']);
        } else {
            // S'assure que l'expéditeur reste synchronisé avec le fichier de config
            self::$mailer->setFrom($config['SMTP_FROM_EMAIL'], $config['SMTP_FROM_NAME'], false);
        }

        // Nettoyer les destinataires précédents
        self::$mailer->clearAddresses();
        self::$mailer->clearAttachments();

        return self::$mailer;
    }

    /**
     * Envoie un email de réinitialisation de mot de passe
     * 
     * @param string $email L'adresse email du destinataire
     * @param string $token Le token de réinitialisation
     * @return bool True si l'email a été envoyé, False sinon
     */
    public static function sendPasswordResetEmail(string $email, string $token): bool {
        try {
            $mail = self::getMailer();
            $mail->addAddress($email);

            $baseUrl = self::getBaseUrl();
            $resetLink = $baseUrl . "?action=reset&email={$email}&token=" . urlencode($token);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe - Factuhello';
            $mail->Body = self::getPasswordResetEmailTemplate($resetLink);
            $mail->AltBody = "Réinitialisation de mot de passe\n\n"
                . "Cliquez sur le lien suivant pour réinitialiser votre mot de passe :\n"
                . $resetLink . "\n\n"
                . "Ce lien expire dans 20 minutes.\n"
                . "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.";

            $mail->send();
            return true;

        } catch (\Throwable $e) {
            error_log("Erreur envoi email reset password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email générique
     * 
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $htmlBody Corps de l'email en HTML
     * @param string $textBody Corps de l'email en texte brut (optionnel)
     * @return bool True si l'email a été envoyé, False sinon
     */
    public static function sendEmail(string $to, string $subject, string $htmlBody, string $textBody = ''): bool {
        try {
            $mail = self::getMailer();
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            $mail->send();
            return true;

        } catch (\Throwable $e) {
            error_log("Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Charge et met en cache la configuration SMTP
     */
    private static function getConfig(): array {
        if (self::$config !== null) {
            return self::$config;
        }

        if (!is_file(self::CONFIG_PATH)) {
            throw new \RuntimeException('Fichier mail.config.ini introuvable');
        }

        $config = parse_ini_file(self::CONFIG_PATH, false, INI_SCANNER_TYPED);
        if ($config === false) {
            throw new \RuntimeException('Impossible de lire mail.config.ini');
        }

        $requiredKeys = [
            'SMTP_HOST',
            'SMTP_PORT',
            'SMTP_USERNAME',
            'SMTP_PASSWORD',
            'SMTP_FROM_EMAIL',
            'SMTP_FROM_NAME',
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config) || $config[$key] === '' || $config[$key] === null) {
                throw new \RuntimeException("Clé manquante ou vide dans mail.config.ini: {$key}");
            }
        }

        self::$config = $config;
        return self::$config;
    }

    /**
     * Obtient l'URL de base du site
     */
    private static function getBaseUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');

        return $protocol . '://' . $host . $scriptName . '/';
    }

    /**
     * Template HTML pour l'email de réinitialisation de mot de passe
     */
    private static function getPasswordResetEmailTemplate(string $resetLink): string {
        return <<<HTML
            <a href="{$resetLink}">{$resetLink}</a>
        HTML;
    }
}
