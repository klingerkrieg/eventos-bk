<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**


* CodeIgniter PDF Library
 *
 * Generate PDF's in your CodeIgniter applications.
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Chris Harvey
 * @license         MIT License
 * @link            https://github.com/chrisnharvey/CodeIgniter-  PDF-Generator-Library



*/

require_once APPPATH.'third_party/dompdf-php7/autoload.inc.php';


function dompdf_img($img){
	return "data:image/svg+xml;base64,".base64_encode(file_get_contents($img));
}


use Dompdf\Dompdf;
class Pdf extends DOMPDF {
	/**
	 * Get an instance of CodeIgniter
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function ci() {
		return get_instance();
	}

	/**
	 * Load a CodeIgniter view into domPDF
	 *
	 * @access  public
	 * @param   string  $view The view to load
	 * @param   array   $data The view data
	 * @return  void
	 */
	public function load_view($view, $data = array(), $file_name=null){

		$dompdf = new Dompdf();
		$data["view"] = $view;
		$html = $this->ci()->load->view("certificados/layout", $data, TRUE);
		#$html = stripslashes(preg_replace('/>\s+</', '><', $html));

		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();
		$time = time();

		$qtd = $dompdf->get_canvas()->get_page_count();
		if ($qtd > 1){
			print "Falha ao gerar certificado.";
			#die();
		}


		if ($file_name != null){
			file_put_contents($file_name, $dompdf->output());
		} else {
			$dompdf->stream("certificado.pdf", array("Attachment" => false));
		}
	}
}