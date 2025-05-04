document.addEventListener("DOMContentLoaded", function () {
    const counterBtns = document.querySelectorAll(".counter-btn");

    counterBtns.forEach(btn =>{
        btn.addEventListener('click', function(){
            const counterContainer = this.parentElement;
            const valueElement = counterContainer.querySelector(".counter-value");
            let value = parseInt(valueElement.textContent);

            if(this.textContent === "+"){
                value++;
            }else if(this.textContent === '-' && value > 1){
                value--;
            }
            valueElement.textContent = value;
        });
    })
});