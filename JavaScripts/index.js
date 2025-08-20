// Hamburger menu
    const hamburger=document.getElementById('hamburger');
    const navLinks=document.getElementById('nav-links');
    hamburger.addEventListener('click',()=>{navLinks.classList.toggle('active')});

    // Slideshow
    const slides=document.getElementById('slides');
    const dotsContainer=document.getElementById('dots');
    const prevBtn=document.getElementById('prev');
    const nextBtn=document.getElementById('next');
    const totalSlides=slides.children.length;
    let index=0;

    // Create dots
    for(let i=0;i<totalSlides;i++){
      let dot=document.createElement('span');
      dot.classList.add('dot');
      if(i===0)dot.classList.add('active');
      dot.addEventListener('click',()=>showSlide(i));
      dotsContainer.appendChild(dot);
    }

    function showSlide(n){
      index=n;
      slides.style.transform=`translateX(${-index*100}%)`;
      document.querySelectorAll('.dot').forEach((dot,i)=>{
        dot.classList.toggle('active',i===index);
      });
    }

    function autoSlide(){
      index=(index+1)%totalSlides;
      showSlide(index);
    }

    // Auto slide
    let slideInterval=setInterval(autoSlide,4000);

    // Manual arrows
    prevBtn.addEventListener('click',()=>{
      index=(index-1+totalSlides)%totalSlides;
      showSlide(index);resetInterval();
    });
    nextBtn.addEventListener('click',()=>{
      index=(index+1)%totalSlides;
      showSlide(index);resetInterval();
    });

    function resetInterval(){
      clearInterval(slideInterval);
      slideInterval=setInterval(autoSlide,4000);
    }

    // ...existing code...
