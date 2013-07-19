busy = true;
function doImageJob(f){
    if(f == false){
	busy = false;
    }else if(busy == true){
	return;
    }else{
	busy = true;
    }

    var signal = document.getElementById('edit_busysignal');
    signal.innerHTML = "";
    if(busy){
	signal.className = 'busy';
    }else{
	signal.className = 'ready';
    }

    if(f != false){
	f();
	doImageJob(false);
    }
}
function reset(){
    if(busy){
	return;
    }
    Pixastic.revert(document.getElementById('pageimg'));
}
function rotate(deg){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'), "rotate",{
	    'angle':deg
	})
    });
}
function mirror(){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'fliph');
    });
}
function invert(){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'invert');
    });
}
function lighten(amt){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'brightness',{
	    'brightness':amt,
	    'legacy':false
	});
    });
}
function contrast(amt){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'brightness',{
	    'contrast':amt
	});
    });
}
function denoise(){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'removenoise');
    });
}
function unsharp(){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'unsharpmask',{
	    'amount':75,
	    'radius':2.2,
	    'threashold':50
	});
    });
}
function zoom(newzoom){
    doImageJob(function(){
	Pixastic.process(document.getElementById('pageimg'),'zoom',{
	    factor:newzoom
	});
    });
}
function listen(evnt, elem, func) {
    // W3C DOM
    if (elem.addEventListener){
	elem.addEventListener(evnt, func, false);
    } else if (elem.attachEvent) {
	// IE DOM
	var r = elem.attachEvent('on'+evnt, func);
	return r;
    }
}

if(!(!!window.CanvasRenderingContext2D) || Pixastic.Client.isIE()){
    // IE9 supports the stuff we need from Pixastic, so we make Pixastic lie.
    if(Pixastic.Client.isIE() && parseFloat(navigator.appVersion.split("MSIE")[1]) >= 9){
	Pixastic.Client.isIE = function(){
	    return false;
	}
	listen('load',window,function(){
	    doImageJob(false);
	});
    }else{
	document.getElementById('pageeditor').innerHTML = "To enable the image \n\
	enhancement tools please use the latest version of any desktop browser. ";
    }

    document.getElementById('pageimgdiv').innerHTML = "";
    var tmpimg = document.getElementById('noncanvaspageimg');
    tmpimg.id = 'pageimg';
    document.getElementById('pageimgdiv').innerHTML = "";
    document.getElementById('pageimgdiv').appendChild(tmpimg);

}else{
    listen('load',window,function(){
	var canvas = document.getElementById('pageimg');
	var img = document.getElementById('noncanvaspageimg');
	canvas.width = img.width;
	canvas.height = img.height;
	var ctx = canvas.getContext('2d');
	ctx.drawImage(img, 0,0);
	doImageJob(false);
    });
}

$(document).ready(function(){
    $('#pageimgdiv').on(
    {
	mousedown: function(clicke){
	    origX = clicke.pageX + $('#pageimgdiv').scrollLeft();
	    $('#pageimgdiv').on(
	    {
		mousemove : function(e){
		    curX = e.pageX + $('#pageimgdiv').scrollLeft();
		    var diff = (origX - curX);
		    var newpos = $('#pageimgdiv').scrollLeft() + diff;
		    if(newpos > ($('canvas').width() - $('#pageimgdiv').width())){
			newpos = ($('canvas').width() - $('#pageimgdiv').width());
		    }
		    if(newpos < 0){
			newpos = 0;
		    }
		    $('#pageimgdiv').scrollLeft(newpos);
		}
	    }
	    );
	},
	mouseleave: function(){
	    $('#pageimgdiv').off('mousemove');
	},
	mouseup: function(){
	    $('#pageimgdiv').off('mousemove');
	},
	click: function(){
	    $('#pageimgdiv').off('mousemove');
	}
    }
    );
});