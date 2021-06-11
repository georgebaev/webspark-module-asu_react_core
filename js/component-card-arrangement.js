(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.cardCarousel = {
    attach: function (context, settings) {
      var componentLoaded = typeof AsuWebCore !== "undefined" && typeof AsuWebCore.initCard !== "undefined";
      var cardExist = typeof settings.asu !== "undefined" && typeof settings.asu.components !== "undefined" && typeof settings.asu.components.card_arrangement !== "undefined";

      if (!cardExist || !componentLoaded) {
        return;
      }

      for (var cardId in settings.asu.components.card_arrangement) {
	      var cardData = settings.asu.components.card_arrangement[cardId];

	      cardData.items.forEach(function(item) {
	        card = settings.asu.components.card[item];
	        //Setup and initialize the Card.
          AsuWebCore.initCard({
            targetSelector: '#card-' + card.id,
            props: {
              type: card.cardType,
              horizontal: false,
              clickable: false,
              image: card.imageSource,
              imageAltText: card.imageAltText,
              title: card.title,
              body: card.content,
              buttons: card.buttons,
              icon: card.icon
            },
          });
	      });

	      delete settings.asu.components.card_arrangement[cardId];
	    }
    }
  };
})(jQuery, Drupal, drupalSettings);