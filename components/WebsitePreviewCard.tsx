"use client";

import { useState } from "react";

type WebsitePreviewCardProps = {
  title: string;
  url: string;
  description: string;
};

function getScreenshotUrl(url: string) {
  return `https://s.wordpress.com/mshots/v1/${encodeURIComponent(url)}?w=1200`;
}

export function WebsitePreviewCard({ title, url, description }: WebsitePreviewCardProps) {
  const [failed, setFailed] = useState(false);

  return (
    <article className="project-card">
      <div className="project-card__thumb">
        {failed ? (
          <div className="project-card__placeholder">À venir</div>
        ) : (
          <img
            src={getScreenshotUrl(url)}
            alt={`Aperçu du site ${title}`}
            onError={() => setFailed(true)}
          />
        )}
      </div>
      <div className="project-card__body">
        <p className="kicker">Référence</p>
        <h3>{title}</h3>
        <p className="section-copy">{description}</p>
        <a className="btn btn--secondary" href={url} target="_blank" rel="noreferrer">
          Ouvrir le site
        </a>
      </div>
    </article>
  );
}
