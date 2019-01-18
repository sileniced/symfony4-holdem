'use strict';

import React from 'react';

const Hands = ({hands, width}) => (
    <div className={'row'}>
        {hands.map(hand => (

            <div key={hand.hand.seat} className={'col-md-' + width + ' player'}>
                <ul>
                    <li>name: {hand.player.name}</li>
                    <li>chips: {hand.player.chips}</li>
                    <li>bet: {hand.hand.chips}</li>
                </ul>

                {hand.hand.cards.map((card, index) => (
                    <img key={index} src={ card.pngName } className={'pkr-card'} />
                ))}

            </div>

        ))}
    </div>
);

export default Hands;