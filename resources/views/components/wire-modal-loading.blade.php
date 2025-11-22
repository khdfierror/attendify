
<x-modal-loading class="fixed inset-0 hidden" id="wire-modal-loading" />

<script >
    document.addEventListener('livewire:initialized', () => {
        
        Livewire.on('openModal', function(){
            document.getElementById("wire-modal-loading")?.classList?.remove("hidden");
        });

        Livewire.on('showModalLoading', function(){
            document.getElementById("wire-modal-loading")?.classList?.remove("hidden");
        });

        Livewire.on("closeWireModelLoading", (modalId) => {
            document.getElementById("wire-modal-loading")?.classList?.add("hidden");
        });
        
        let loadingTimeout;
    
        document.addEventListener("livewire:navigate", (event) => {
            loadingTimeout = setTimeout(() => {
                document.getElementById("wire-modal-loading")?.classList?.remove("hidden");
            }, 700);
        });
        
        document.addEventListener("livewire:navigating", (event) => {
            //
        });
        
        document.addEventListener("livewire:navigated", (event) => {
            clearTimeout(loadingTimeout);
            document.getElementById("wire-modal-loading")?.classList?.add("hidden");
        });
    });

</script>
