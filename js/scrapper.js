const axios = require('axios');
const cheerio = require('cheerio');
const tnp = require('torrent-name-parser');

const toJSON = (data) => {
    const processed = {
        title: null,
        magnet: null
    }
    return Object.assign(processed, data)
}

const enrich = (movie, res) => {
	return {
		...movie,
		imdb: res.imdbID,
		title: res.Title,
		rating: parseInt(res.imdbRating),
		year: parseInt(res.Year),
		image: res.Poster,
		genres: ',' + res.Genre + ',',
	};
}

const dataIsValid = (res) => {
	if (res.Response == false)
		return false;
	if (!res.imdbID || !res.imdbID.match(/tt.*/ig))
		return false;
	if (!res.Poster || !res.Poster.match(/https?:\/\/.*/ig))
		return false;
	if (!(parseInt(res.Year) >= 1990 && parseInt(res.Year) <= 2018))
		return false;
	return true;
}

const parseHash = (magnet) => {
	const regex = /magnet:\?xt=urn:btih:(.*?)&/;
	const hash = regex.exec(magnet);
	return hash[1];
}

const scrapThePirateBay = async () => {
    const list = [];
	const movies = [];
    const url = 'https://thepiratebay.org/top/201';

    const result = await axios.get(url);
	const $ = cheerio.load(result.data);

    $('#searchResult tr')
        .each((i, elem) => {
            let title = $(elem).find('td').eq(1).find('.detName a').text();
            let magnet = $(elem).find('td').eq(1).find('.detName + a').attr('href');
			if (title) {
				title = tnp(title).title;
			}
            if (magnet && title) {
				let hash = parseHash(magnet);
                list.push(toJSON({ magnet, title, hash }));
			}
        })
	
	for (let i = 0; i < list.length; i++) {
		try {
			let res = await axios.get('http://www.omdbapi.com/?t=' + list[i].title + '&apikey=6570dfea')
			if (dataIsValid(res.data)) {
				movies.push(enrich(list[i], res.data));
			}
			if (i + 1 == list.length) {
				console.log(JSON.stringify(movies))
			}
		} catch (e) {
			console.log(e);
		}
	}
}

scrapThePirateBay();
