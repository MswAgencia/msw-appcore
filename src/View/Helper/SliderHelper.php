<?php

namespace AppCore\View\Helper;

use Cake\View\Helper;
use Cake\Utility\String;
use Cake\Utility\Hash;

/**
 * SliderHelper.
 * 
 * Um helper que monta a estrutura HTML de determinados sliders.
 * 
 * @since  1.0.0
 * @version  1.0.0
 * 
 */
class SliderHelper extends Helper {
	public $helpers = ['Html'];
		
	/**
	 * Função auxiliar para montar a estrutura HTML do flexslider.
	 * @param array $slides Um array com as informações de cada slide.
	 * Formato:
	 * $slides = array(
	 * 				0 => array(
	 *     					'img_src' => '/my/img/source/file.jpg'
	 *          			'img_title' => 'The Image Title',
	 *             			'img_description' => 'This is the description of the awesome image',
	 *          			'img_options' => array(the_options),
	 *             			'img_link' => 'http://www.thecoolwebsite.com/',
	 *                		'link_options' => array(the_options),
	 *                  	'li_options' => array(the_options),
	 *          			  ),
	 *               ....
	 * );
	 * 'img_src': É o caminho da imagem a partir de webroot/img.
	 * 'img_options': Opções para a imagem (html tag <img>). Mesmo formato para a função HtmlHelper::image().
	 * 'img_title': O título da imagem.
	 * 'img_description': A descrição da imagem.
	 * 'img_link': A url para onde o link irá apontar.
	 * 'link_options': Opções para o link (html tag <a>). Mesmo formato para a função HtmlHelper::link().
	 * 'li_options': Opções para a tag li. Quando for necessário uma configuração diferente para um slide (li) específico, use esta opção. Esta opção sobrescreve a opção global para tag li. array de opções como em HtmlHelper::tag().
	 * 
	 * @param  array $options Um array com opções de configuração.
	 * Formato:
	 * $options = array(
	 * 				'div' => array(the_options),
	 *     			'ul' => array(the_options),
	 *        		'li' => array(the_options),
	 *        		'slide_template' => ':link_image<p class='my_class'>:description</p>',
	 *          	'webroot' => 'site',
	 * );
	 * 'div': array de opções como em HtmlHelper::tag().
	 * 'ul': array de opções como em HtmlHelper::tag().
	 * 'li': array de opções como em HtmlHelper::tag().
	 * 'webroot': string, 'app' ou 'site', para escolher de qual webroot será lido a imagem. Se é do app ou se é do plugin Site.
	 * 'slide_template': a formatação do li, caso precise alterá-lo. Padrão ':image'. Não informar <li></li>.
	 * Em slide_template há "variáveis" que podem ser usadas, sendo elas:
	 * :link_image Um exemplo de saída seria <a href="/to/my/page"><img src="/myimg.jpg"></a>
	 * :image  <img src="/myimg.jpg">
	 * :description Uma string para ser usada como descrição.
	 * :title Uma string para ser usada como título.
	 * @throws  InvalidArgumentException Caso $slides[x]['img_src'] esteja vazio, ele lança uma exception.
	 * @return  string Toda a estrutura do flexslider em HTML com cada slide de acordo com os valores informados por parâmetro.
	 */
	public function flexslider($slides, $options = array()){
		// validações iniciais
		if(!isset($options['slide_template'])):
			$options['slide_template'] = ':image';
		endif;
		if(!isset($options['li'])):
			$options['li'] = array();
		endif;
		if(!isset($options['webroot'])):
			$options['webroot'] = 'site';
		endif;
		switch($options['webroot']){
			case 'app':
				$webDir = '';
			break;
			case 'site':
			default:
				$webDir = 'Site.';
		}
		$options['div'] = $this->checkFlexSliderDivOptions($options);
		$options['ul'] = $this->checkFlexSliderUlOptions($options);

		// gera o <li> primeiro
		$liHtml = '';
		foreach ($slides as $slide):
			// verifica se existe opções para a tag <img>, senão existe, cria um array vazio para não dar problema.
			if(!isset($slide['img_options'])):
				$slide['img_options'] = array();
			endif;
			// verifica se existe opções para a tag <a>, senão existe, cria um array com a opção escape => false. Se existe ele faz um merge com a opção escape => false.
			if(!isset($slide['link_options'])):
				$slide['link_options'] = array('escape' => false);
			else:
				$slide['link_options'] = Hash::merge($slide['link_options'], array('escape' => false));
			endif;
			// verifica se existe descrição, senão existe, cria uma string vazia.
			if(!isset($slide['img_description'])):
				$slide['img_description'] = '';
			endif;
			// verifica se existe titulo, senão existe, cria uma string vazia.
			if(!isset($slide['img_title'])):
				$slide['img_title'] = '';
			endif;
			// verifica se existe opção específica para o slide(li). Se existe, junta essas opções com as opções globais e guarda para ser usado ao criar o elemento li. Senão, guarda as opções globais para serem usadas.
			if(isset($slide['li_options']))
				$liOptions = array_merge($options['li'], $slide['li_options']);
			else
				$liOptions = $options['li'];

			$image = $this->Html->image($webDir.$slide['img_src'], $slide['img_options']);
			if(isset($slide['image_link'])):
				$image_link = $this->Html->link($image, $slide['image_link'], $slide['link_options']);
			else:
				$image_link = '';
			endif;

			$liContent = String::insert($options['slide_template'], array('image' => $image, 'image_link' => $image_link, 'description' => $slide['img_description'], 'title' => $slide['img_title']));

			$liHtml .= $this->Html->tag('li', $liContent, $liOptions);
		endforeach;
		// depois gera o <ul> e coloca os <li> dentro do <ul>
		$ulHtml = $this->Html->tag('ul', $liHtml, $options['ul']);
		// depois gera a <div> final e coloca o <ul> dentro dela.
		$generatedHtml = $this->Html->tag('div', $ulHtml, $options['div']);
		// finished!
		return $generatedHtml;
	}

	/**
	 * Função usada para verificar se as opções da 'div' do flexslider existem. Se não existirem, retorna as opções padrão.
	 * @param  array $options @see SliderHelper::flexslider()
	 * @return  array Um array com as opções da 'div' prontas para uso.
	 */
	private function checkFlexSliderDivOptions($options){
		if(!isset($options['div'])):
			$options['div'] = array('class' => 'flexslider');
		elseif(!isset($options['div']['class'])):
			$options['div']['class'] = 'flexslider';
		else:
			$options['div']['class'] .= ' flexslider';
		endif;
		return $options['div'];
	}

	/**
	 * Função usada para verificar se as opções da 'ul' do flexslider existem. Se não existirem, retorna as opções padrão.
	 * @param  array $options @see SliderHelper::flexslider()
	 * @return  array Um array com as opções da 'ul' prontas para uso.
	 */
	private function checkFlexSliderUlOptions($options){
		if(!isset($options['ul'])):
			$options['ul'] = array('class' => 'slides');
		elseif(!isset($options['ul']['class'])):
			$options['ul']['class'] = 'slides';
		else:
			$options['ul']['class'] .= ' slides';
		endif;
		return $options['ul'];
	}

	/**
	 * Monta o código jQuery para executar a função javascript do flexslider.
	 * 
	 * @param  string $selector O seletor jquery do elemento onde deve ser executado o flexslider. Padrão '.flexslider'.
	 * @param  array $options Um array com opções. São as mesmas existentes nas configurações do flexslider.
	 * @return  string Retorna o código jquery pronto para ser escrito dentro de uma tag <style></style>.
	 */
	public function runFlexslider($selector = '.flexslider', $options = array()){
		$code = '$("' . $selector . '").flexslider(';
		if(!empty($options))
			$code .= json_encode($options);
		
		$code .= ');';
		return $code;
	}
}