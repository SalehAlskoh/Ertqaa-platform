<dialog class="confirmation-dialog" id="confirmation-dialog" aria-labelledby="confirmation-title" aria-describedby="confirmation-description">
    <div class="confirmation-content">
        <span class="confirmation-icon"><x-icon name="alert" :size="25" /></span>
        <h2 id="confirmation-title">تأكيد الإجراء</h2>
        <p id="confirmation-description" data-confirmation-description></p>
    </div>
    <div class="confirmation-actions"><button class="button button-secondary" type="button" data-confirmation-cancel autofocus>إلغاء والعودة</button><button class="button" type="button" data-confirmation-approve>تأكيد الإجراء</button></div>
</dialog>
