.PHONY: web

web:
	cd Website && hugo -d ../site

web_serve:
	cd Website && hugo serve