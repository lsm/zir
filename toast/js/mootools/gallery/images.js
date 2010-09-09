var thumbToOpen = -1;
var thumbToClose = -1;

function openThumbs(selected,open){
	var thumbs = $$('.thumbnailContainer');
	var thumbFx = new Fx.Elements(thumbs, {wait: false, duration: 400, transition: Fx.Transitions.quadOut, onComplete: nextThumbs});
	var thumbFx2 = new Fx.Elements(thumbs, {wait: false, duration: 400, transition: Fx.Transitions.quadIn, onComplete: nextThumbs});
	
	thumbToOpen = selected;
	
	var obj = {};
	if(selected == -1){
		obj[thumbToClose] = {
			'height': [thumbs[thumbToClose].getStyle('height').toInt(), 0]
		};
		thumbFx.start(obj);
	}else{
		if(thumbToClose == -1){
			thumbs[selected].setStyle('display', 'block');
			obj[selected] = {
				'height': [thumbs[selected].getStyle('height').toInt(), 550]
			};
			thumbFx.start(obj);
			thumbToClose = selected;
		}else{
			obj[thumbToClose] = {
				'height': [thumbs[thumbToClose].getStyle('height').toInt(), 0]
			};
			thumbFx2.start(obj);
		}
	}
}

function nextThumbs(){
	var thumbs = $$('.thumbnailContainer');
	var thumbFx = new Fx.Elements(thumbs, {wait: false, duration: 400, transition: Fx.Transitions.quadOut});
	
	if(thumbToClose != -1){
		thumbs[thumbToClose].setStyle('display', 'none');
		thumbToClose = -1;
	if(thumbToOpen != -1){
		thumbs[thumbToOpen].setStyle('display', 'block');
		var obj = {};
		obj[thumbToOpen] = {
			'height': [thumbs[thumbToOpen].getStyle('height').toInt(), 550]
		};
		thumbFx.start(obj);
		thumbToClose = thumbToOpen;
	}
	}
}

var imagesStr = '<p><a href="digital_art/images/42_seconds_of_a_static_lullaby_by_Jesar.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_42_seconds_of_a_static_lullaby_by_Jesar.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/A_Waste____by_sphereuk.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_A_Waste____by_sphereuk.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/Catamaran.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_Catamaran.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/Colorpicker_a_by_iuneWind.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_Colorpicker_a_by_iuneWind.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/ElementOneSkylineREMIX_by_Jesar.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_ElementOneSkylineREMIX_by_Jesar.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/Empower.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_Empower.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/every_day_life_by_Jesar.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb_every_day_life_by_Jesar.jpg" border="0" /></div></a><div class="lightboxDesc"></div>'
	+ '<p><a href="digital_art/images/__Fire_Storm___by_kiddd7.jpg" rel="lightbox" ><div class="horizontal">'
	+ '<img src="digital_art/images/thumb___Fire_Storm___by_kiddd7.jpg" border="0" /></div></a><div class="lightboxDesc"></div></p>';