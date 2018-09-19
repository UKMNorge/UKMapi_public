<?php
	
class json_export {
	
	public static function commons( $inp_object ) {
		$object 			= new stdClass();
		$object->id			= $inp_object->getId();
		$object->navn		= $inp_object->getNavn();
		$object->sted		= $inp_object->getSted();
		
		return $object;
	}
	
	public static function monstring( $monstring ) {
		$object 			= self::commons( $monstring );
		$object->start		= self::dato( $monstring->getStart() );
		$object->stop		= self::dato( $monstring->getStop() );
		$object->type		= $monstring->getType();
		
		return $object;
	}

	public static function dato( $date ) {
		return $date->format('c');
	}

	public static function hendelse( $hendelse ) {
		$object 				= self::commons( $hendelse );
		$object->start			= self::dato( $hendelse->getStart() );
		$object->type			= $hendelse->getType();
		$object->intern			= $hendelse->erIntern();
		$object->detaljer		= $hendelse->erSynligDetaljprogram();
		
		$object->post_id		= $hendelse->getTypePostId();
		$object->category_id	= $hendelse->getTypeCategoryId();
		return $object;
	}
	
	public static function innslag( $innslag ) {
		$object 						= new stdClass();
		$object->id						= $innslag->getId();
		$object->navn					= $innslag->getNavn();
		$object->type					= self::innslag_type( $innslag->getType() );
		$object->beskrivelse			= $innslag->getBeskrivelse();
		$object->kommune				= self::kommune( $innslag->getKommune() );
		$object->kategori				= $innslag->getKategori();
		$object->sjanger				= $innslag->getSjanger();
		$object->kategori_og_sjanger	= $innslag->getKategoriOgSjanger();
		
		try {
			$bilde 						= $innslag->getBilder()->getFirst();
			$object->bilde				= self::bilde( $bilde, 'thumbnail' );
		} catch( Exception $e ) {
			$object->bilde 				= self::placeholder( 'https://grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png' );
		}

		return $object;
	}
	
	
	public static function placeholder( $url='https://grafikk.ukm.no/profil/logoer/UKM_logo_sort_0100.png' ) {
		$object 				= new stdClass();
		$object->url			= $url;
		$object->width			= 100;
		$object->height			= 56;
		$object->orientation	= 'landscape';
		
		return $object;
	}
	public static function bilde( $bilde, $size='lite' ) {
		if( is_object( $bilde ) ) {
			$size = $bilde->getSize( $size );
		} else {
			return self::placeholder();
		}
		
		$object 				= self::placeholder( $size->getUrl() );
		$object->width			= $size->getWidth();
		$object->height			= $size->getHeight();
		$object->orientation	= $size->getOrientation();
		return $object;
	}

	public static function artikkel( $artikkel ) {
		$object = new stdClass();
		$object->blog				= new stdClass();
		$object->monstring			= new stdClass();

		$object->id					= $artikkel->getId();
		$object->navn				= $artikkel->getTittel();
		$object->url				= $artikkel->getLink();
		$object->monstring->type	= $artikkel->getMonstringType();
		$object->monstring->sesong	= $artikkel->getSeason();
		$object->blog->id			= $artikkel->getBlogId();
		$object->blog->url			= $artikkel->getBlogPath();
		
		return $object;
	}

	public static function tv( $tv ) {
		$object = new stdClass();
		$object->kategori			= new stdClass();
		$object->samling			= new stdClass();
		$object->bilde 				= new stdClass();

		$object->bilde->url			= $tv->image_url;
		$object->bilde->width		= 1280;
		$object->bilde->height		= 720;
		$object->bilde->orientation	= 'landscape';

		$object->url				= $tv->full_url;
		$object->navn				= $tv->title;

		$object->samling->url 		= $tv->set_url;
		$object->samling->navn		= $tv->set;

		$object->kategori->url 		= $tv->category_url;
		$object->kategori->navn		= $tv->category;

		return $object;
	}
	
	public static function innslag_type( $type ) {
		$object					= new stdClass();
		$object->id				= $type->getId();
		$object->navn			= $type->getNavn();
		$object->key			= $type->getKey();
		$object->ikon			= $type->getIcon();
		$object->harTid			= $type->harTid();
		$object->harTitler		= $type->harTitler();
		return $object;
	}
	
	public static function kommune( $kommune ) {
		$object					= new stdClass();
		$object->id				= $kommune->getId();
		$object->navn			= $kommune->getNavn();
		$object->fylke			= new stdClass();
		$object->fylke->id		= $kommune->getFylke()->getId();
		$object->fylke->navn	= $kommune->getFylke()->getNavn();
		return $object;
	}
}