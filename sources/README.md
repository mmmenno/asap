# ASAP Sources

## BAG buildings construction years

The Basisadministratie Adressen en Gebouwen (BAG) contains every building in the Netherlands. The construction dates of buildings are not always very thrustworthy - lots of buildings are said to be built in 1005, meaning, I guess, something like "probably rather old".

The BAG can be accessed through a [SPARQL endpoint](https://data.pdok.nl/sparql#), in which I used the following query to extract the [oldest building of each street](BAG-oldest-building-of-street.csv):

```
PREFIX bag: <http://bag.basisregistraties.overheid.nl/def/bag#>
select DISTINCT ?street ?straatnaam (MIN(?bouwjaar) AS ?minbouwjaar)
where {
  ?na bag:bijbehorendeOpenbareRuimte ?street .
  ?street bag:naamOpenbareRuimte ?straatnaam .
  ?street bag:bijbehorendeWoonplaats <http://bag.basisregistraties.overheid.nl/bag/id/woonplaats/3594> .
  ?vo bag:hoofdadres ?na .
  ?vo bag:pandrelatering ?pand .
  ?pand bag:oorspronkelijkBouwjaar ?bouwjaar .
  FILTER (?bouwjaar > 1005)
}
GROUP BY ?street ?straatnaam
```
Expectations are the BAG construction years will be especially useful to date more recent streets that lack raadsbesluiten.


## Raadsbesluiten

The raadsbesluiten where originally collected for *Martha Bakker (red.)*, __Stadsatlas Amsterdam - Stadskaarten & Straatnamen verklaard, 1998__. The data were updated for a reprint in 2006. In [raadsbesluiten.csv](raadsbesluiten.csv) years are extracted from the original text and URIs are added.

## Transportakten

Coming soon.

## Streets within 'Uitleggen' or Expansions

We know (roughly) when different parts of the city were built. Most famous is perhaps the 'Derde Uitleg', in which the first part of the 'grachtengordel' and the Jordaan were developed. [This 1882 map showing the different expansions](https://beeldbank.amsterdam.nl/afbeelding/D10098000073) was a big help. So was the [Amsterdam Museum Groeikaart](https://hart.amsterdam/image/2017/5/17/2013_groeikaart_amsterdam_1000_2000.pdf).

I [vectorised](stadsuitbreidingen.geojson) these cityparts, so I could easily select the 1100+ streets within these parts and mark them accordingly. The results are in [straten-stadsuitbreidingen.csv](straten-stadsuitbreidingen.csv).

Please note that this approach on itself gives clues, not facts. The [Regulierspad](https://adamlink.nl/geo/street/regulierspad/6962) *disappeared* with the 'Vierde Uitleg', the [Pentagon](https://adamlink.nl/geo/street/pentagon/3514) was built in the 1980's.

# Maps

When a street appears on a map (and it's not a planners map) we can safely assume the street existed when the map was created.

## Jodocus Hondius Map 1612

[The Hondius Map](https://beeldbank.amsterdam.nl/afbeelding/010001000605) lacks geometric accuracy, but names all the 'paden' (paths) outside the city walls that disappeared shortly after with the expansions of the city.

The file [map-hondius-1612.csv](map-hondius-1612.csv) lists all the streetnames on the map and identifies them with an Adamlink URI.


## Berckenrode Map 1625

[The Berckenrode Map](https://beeldbank.amsterdam.nl/afbeelding/010035000349) is probably the most detailed 17th-century map of the city, made just after the 'Derde Uitleg', in which the western part of the Grachtengordel and the Jordaan where created.

- [map-berckenrode-1625-nieuwezijds.csv](map-berckenrode-1625-nieuwezijds.csv) lists the streetnames on the 'Nieuwe Sijde' (the New Side, or leftbank) of town.
- [map-berckenrode-1625-oudezijds.csv](map-berckenrode-1625-oudezijds.csv) lists the streetnames on the 'Oude Sijde' (the Old Side, or rightbank) of town.
- [map-berckenrode-1625-names-on-map.csv](map-berckenrode-1625-names-on-map.csv) lists most of the names (we might have missed some) on the map itself.

## Ram Map 1692 

On the [Ram Map](https://beeldbank.amsterdam.nl/afbeelding/KOKA00098000001) the expansions (4e Uitleg 1658-1662) from the Leidsegracht to and across the Amstel are visible. 

The file [map-ram-1692.csv](map-ram-1692.csv) lists all the streetnames on the map and identifies them with an Adamlink URI.

## Presence on Maps

The map data above helps to make a [list of al the maps a street is or is not depicted on](present-on-maps.csv). For every named gang in the Jordaan on Loman I manually checked the last map it was depicted on and the first map it was not.

