# TODO: Update ia_alertes table and related code for polymorphic recipients

- [x] Update app/Models/Ia_alerte.php: Modify fillable to include 'destinataire_type', 'destinataire_id', 'lu' and remove 'idAdmi'. Remove administrateur() relationship and add destinataire() morphTo relationship.
- [x] Update app/Services/IAService.php: Change CrerAlerte method signature to accept $destinataireType and $destinataireId instead of $adminId, and adjust the create array accordingly.
- [x] Run php artisan migrate to apply the migration.
- [x] Test the changes by creating alerts for admins and sellers.
