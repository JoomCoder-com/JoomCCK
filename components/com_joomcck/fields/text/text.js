function initMask(id, mask, type)
 {
	oTextMask = new Mask(mask, type);
	oTextMask.attach($("field_" + id));
}