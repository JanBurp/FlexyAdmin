<?

function help($s,$help) {
	// return $s.img(admin_assets("icons/file.gif"));
	// return $s.icon("help");
	return span("help").$s.span("hide").$help._span()._span();
}

?>
