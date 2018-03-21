$( document ).ready(function() {
	jQuery(".slider-pack").slick({
		  dots: false,
		  infinite: false,
		  speed: 300,
		  slidesToShow: 4,
		  slidesToScroll: 4,
		  responsive: [
		    {
		      breakpoint: 1024,
		      settings: {
		        slidesToShow: 3,
		        slidesToScroll: 3,
		        infinite: true
		      }
		    },
		    {
		      breakpoint: 768,
		      settings: {
		        slidesToShow: 2,
		        slidesToScroll: 2
		      }
		    },
		    {
		      breakpoint: 480,
		      settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1
		      }
		    }
		    // You can unslick at a given breakpoint now by adding:
		    // settings: "unslick"
		    // instead of a settings object
		  ]
	});
	jQuery(".slider-inner").slick({
		  dots: false,
		  infinite: false,
		  speed: 300,
		  slidesToShow: 3,
		  slidesToScroll: 3,
		  variableWidth: true,
		  responsive: [
		    {
		      breakpoint: 1024,
		      settings: {
		        slidesToShow: 3,
		        slidesToScroll: 3,
		        infinite: true
		      }
		    },
		    {
		      breakpoint: 600,
		      settings: {
		        slidesToShow: 2,
		        slidesToScroll: 2
		      }
		    },
		    {
		      breakpoint: 480,
		      settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        variableWidth: true
		      }
		    }
		  ]
	});
	jQuery(".slider-services").slick( {
	        dots:!1, 
	        infinite:!1, 
	        speed:300, 
	        slidesToShow:3, 
	        slidesToScroll:3, 
	        variableWidth: true, 
	        responsive:[ {
	            breakpoint:1024, 
	            settings: {
	                slidesToShow: 3, 
	                slidesToScroll: 3, 
	                infinite: !0
	            }
	        }
	        , {
	            breakpoint: 600, 
	            settings: {
	                slidesToShow: 2,
	                slidesToScroll: 2
	            }
	        }
	        , {
	            breakpoint:480, 
	            settings: {
	                slidesToShow: 1, 
	                slidesToScroll: 1,
	                variableWidth: false
	            }
	        }
	        ]
	});
	jQuery(".slider-services-detail").slick({
	    dots: false,
	    infinite: true,
	    speed: 300,
	    slidesToShow: 1,
	    adaptiveHeight: true
	  });
});	